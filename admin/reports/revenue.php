<?php
include '../includes/header.php';
include '../includes/db.php';

// --- XỬ LÝ BỘ LỌC NGÀY THÁNG ---
// Mặc định: Lấy 30 ngày gần nhất
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));

// --- TRUY VẤN DỮ LIỆU ---
// Chỉ lấy các đơn hàng ĐÃ HOÀN THÀNH (id_trang_thai = 4)
$sql = "
    SELECT 
        DATE(dh.ngay_gio_tao_don) as ngay,
        COUNT(DISTINCT dh.id_don_hang) as so_don_hang,
        SUM(ct.so_luong_ban * ct.don_gia_ban) as doanh_thu
    FROM don_hang dh
    JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
    WHERE dh.id_trang_thai = 4 
    AND DATE(dh.ngay_gio_tao_don) BETWEEN :from_date AND :to_date
    GROUP BY DATE(dh.ngay_gio_tao_don)
    ORDER BY ngay ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':from_date' => $from_date,
    ':to_date' => $to_date
]);
$data = $stmt->fetchAll();

// --- XỬ LÝ DỮ LIỆU ĐỂ VẼ BIỂU ĐỒ & TÍNH TỔNG ---
$chart_labels = []; // Mảng chứa ngày (Trục X)
$chart_data = [];   // Mảng chứa doanh thu (Trục Y)
$total_revenue = 0;
$total_orders = 0;

foreach ($data as $row) {
    // Format ngày cho đẹp (d/m)
    $chart_labels[] = date('d/m', strtotime($row['ngay']));
    $chart_data[] = (int)$row['doanh_thu'];
    
    $total_revenue += $row['doanh_thu'];
    $total_orders += $row['so_don_hang'];
}

// Chuyển đổi mảng PHP sang JSON để dùng trong JavaScript
$json_labels = json_encode($chart_labels);
$json_data = json_encode($chart_data);
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Báo Cáo Doanh Thu</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Doanh Thu</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card mb-3">
                <div class="card-body p-3">
                    <form action="" method="GET" class="form-inline">
                        <label class="mr-2">Từ ngày:</label>
                        <input type="date" name="from_date" class="form-control mr-3" value="<?php echo $from_date; ?>" required>
                        
                        <label class="mr-2">Đến ngày:</label>
                        <input type="date" name="to_date" class="form-control mr-3" value="<?php echo $to_date; ?>" required>
                        
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Lọc Dữ Liệu</button>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo number_format($total_revenue, 0, ',', '.'); ?> <sup style="font-size: 20px">đ</sup></h3>
                            <p>Tổng Doanh Thu (Đã Hoàn Thành)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo number_format($total_orders); ?></h3>
                            <p>Tổng Đơn Hàng (Đã Hoàn Thành)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar mr-1"></i>
                                Biểu Đồ Doanh Thu
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="revenueChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Chi Tiết Theo Ngày</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th class="text-center">Đơn</th>
                                        <th class="text-right">Doanh Thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($data)): ?>
                                        <tr><td colspan="3" class="text-center">Không có dữ liệu trong khoảng thời gian này.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['ngay'])); ?></td>
                                            <td class="text-center"><?php echo $row['so_don_hang']; ?></td>
                                            <td class="text-right font-weight-bold text-success">
                                                <?php echo number_format($row['doanh_thu'], 0, ',', '.'); ?> đ
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function () {
    // Lấy dữ liệu từ PHP
    var areaChartData = {
      labels  : <?php echo $json_labels; ?>,
      datasets: [
        {
          label               : 'Doanh Thu (VNĐ)',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : 4,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : <?php echo $json_data; ?>,
          fill                : false, // Biểu đồ đường không tô màu nền dưới
          tension             : 0.3    // Độ cong của đường
        }
      ]
    }

    // Cấu hình Chart
    var barChartCanvas = $('#revenueChart').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, areaChartData)

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false,
      scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value, index, values) {
                    // Format trục Y thành tiền Việt
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                }
            }
        }
      },
      plugins: {
          tooltip: {
              callbacks: {
                  label: function(context) {
                      let label = context.dataset.label || '';
                      if (label) {
                          label += ': ';
                      }
                      if (context.parsed.y !== null) {
                          label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                      }
                      return label;
                  }
              }
          }
      }
    }

    new Chart(barChartCanvas, {
      type: 'line', 
      data: barChartData,
      options: barChartOptions
    })
})
</script>