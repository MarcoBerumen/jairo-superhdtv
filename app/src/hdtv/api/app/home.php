<?php

use Imx\html;

dispatch("/app/home", function () {

    html::head("Dashboard");
    html::bodyInit();
    ?>
    <style>
        .sidebar{
            width: 0;
            background-color: #222;
            height: 200px;
        }
        body.open .sidebar{
            width: 200px;
        }

    </style>
    <?php
    // html::header("");
    // html::sidebar();
    html::beginContent();
//    $user = $_GET['user']??7;
    $token = getallheaders()['Authorization'] ?? "";

     $user = Imx\db::dataQuery("select
     users.user_id,
     users.name
     from users 
     where token ='{$token}'
     ");
//    $user['name'] ="josue";
//    $user['user_id'] = $_GET['user']??7;

    $sales = Imx\db::rquery("select sum(subtotal) from sales 
    where year(date) = year(now()) and month(date) = month(now()) and user_id ='{$user['user_id']}'") ?? 0;
    // Previous month sales
    $salesp = Imx\db::rquery("select sum(subtotal) from sales 
    where year(date) = year(date_sub(curdate() , interval 1 month)) and month(date) = month(date_sub(curdate() , interval 1 month)) and user_id ='{$user['user_id']}'") ?? 0;
            $percent = ($sales - $salesp)*100;
            $percent = 0;
            if($salesp){
            $percent =$percent/ $salesp;
            }


    $salesmonth = Imx\db::dataQueryMultiple("select sum(subtotal) as total,date from sales 
    where year(date) = year(now()) and month(date) = month(now()) and user_id ='{$user['user_id']}' group by date") ?? [];
    $receipts = Imx\db::rquery("select count(*) from sales 
    where year(date) = year(now()) and month(date) = month(now()) and user_id ='{$user['user_id']}'") ?? 0;
    $avg = Imx\utils::decimales2(Imx\db::rquery("select avg(subtotal) from sales 
    where year(date) = year(now()) and month(date) = month(now()) and user_id ='{$user['user_id']}'") ?? 0);

?>
    <link href="/assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
    <link href="/assets/plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
    <link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
    <!-- ================== END page-css ================== -->
    <h1 class="page-header mb-3">Welcome <?php echo $user['name'];?></h1>
    <!-- END daterange-filter -->
    <!-- BEGIN row -->
    <div class="row">
        <!-- BEGIN col-6 -->
        <div class="col-xl-6">
            <!-- BEGIN card -->
            <div class="card border-0 mb-3 overflow-hidden bg-gray-800 text-white">
                <!-- BEGIN card-body -->
                <div class="card-body">
                    <!-- BEGIN row -->
                    <div class="row">
                        <!-- BEGIN col-7 -->
                        <div class="col-xl-7 col-lg-8">
                            <!-- BEGIN title -->
                            <div class="mb-3 text-gray-500">
                                <b>TOTAL SALES</b>
                                <span class="ms-2">
                                    <i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Total sales" data-bs-placement="top" data-bs-content="Net sales (gross sales minus discounts and returns) plus taxes and shipping. Includes orders from all sales channels."></i>
                                </span>
                            </div>
                            <!-- END title -->
                            <!-- BEGIN total-sales -->
                            <div class="d-flex mb-1">
                                <h2 class="mb-0">$<span data-animation="number" data-value="<?php echo $sales; ?>">0.00</span></h2>
                                <div class="ms-auto mt-n1 mb-n1">
                                    <div id="total-sales-sparkline"></div>
                                </div>
                            </div>
                            <!-- END total-sales -->
                            <!-- BEGIN percentage -->
                            <div class="mb-3 text-gray-500">
                                <i class="fa fa-caret-<?php
                                if($percent > 0){
                                    echo "up";
                                }
                                else
                                {
                                    echo "down";
                                }
                                $percent =  Imx\utils::decimales2($percent);
                                ?>"></i> <span data-animation="number" data-value="<?php echo $percent;?>">0.00</span>% compare to last moth (<?php echo Imx\utils::decimales2($salesp);?>)
                            </div>
                            <!-- END percentage -->
                            <hr class="bg-white bg-opacity-50" />
                            <!-- BEGIN row -->
                            <div class="row text-truncate">
                                <!-- BEGIN col-6 -->
                                <div class="col-6">
                                    <div class=" text-gray-500">Total sales order</div>
                                    <div class="fs-18px mb-5px fw-bold" data-animation="number" data-value="<?php echo $receipts; ?>">0</div>
                                    <div class="progress h-5px rounded-3 bg-gray-900 mb-5px">
                                        <div class="progress-bar progress-bar-striped rounded-right bg-teal" data-animation="width" data-value="55%" style="width: 0%"></div>
                                    </div>
                                </div>
                                <!-- END col-6 -->
                                <!-- BEGIN col-6 -->
                                <div class="col-6">
                                    <div class=" text-gray-500">Avg. sales per order</div>
                                    <div class="fs-18px mb-5px fw-bold">$<span data-animation="number" data-value="<?php echo $avg; ?>">0.00</span></div>
                                    <div class="progress h-5px rounded-3 bg-gray-900 mb-5px">
                                        <div class="progress-bar progress-bar-striped rounded-right" data-animation="width" data-value="55%" style="width: 0%"></div>
                                    </div>
                                </div>
                                <!-- END col-6 -->
                            </div>
                            <!-- END row -->
                        </div>
                        <!-- END col-7 -->
                        <!-- BEGIN col-5 -->
                        <div class="col-xl-5 col-lg-4 align-items-center d-flex justify-content-center">
                            <img src="../assets/img/svg/img-1.svg" height="150px" class="d-none d-lg-block" />
                        </div>
                        <!-- END col-5 -->
                    </div>
                    <!-- END row -->
                </div>
                <!-- END card-body -->
            </div>
            <!-- END card -->
        </div>
        <!-- END col-6 -->
        <!-- BEGIN col-6 -->
        <!-- END col-6 -->
    </div>
    <!-- END row -->
    <!-- BEGIN row -->
    <!-- END row -->
    <!-- BEGIN row -->
    <!-- END row -->

    <?php


    html::endContent();
    html::containerEnd();
    html::scripts();
    ?>
    <script src="/assets/plugins/d3/d3.min.js"></script>
    <script src="/assets/plugins/nvd3/build/nv.d3.min.js"></script>
    <script src="/assets/plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="/assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
    <script src="/assets/plugins/apexcharts/dist/apexcharts.min.js"></script>
    <script src="/assets/plugins/moment/min/moment.min.js"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>


        var handleTotalSalesSparkline = function() {
            var options = {
                chart: {
                    type: 'line',
                    width: 200,
                    height: 36,
                    sparkline: {
                        enabled: true
                    },
                    stacked: true
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 1,
                        opacityTo: 1,
                        colorStops: [{
                                offset: 0,
                                color: COLOR_BLUE,
                                opacity: 1
                            },
                            {
                                offset: 100,
                                color: COLOR_INDIGO,
                                opacity: 1
                            }
                        ]
                    },
                },
                series: [{
                    data: [<?php
                        foreach($salesmonth as $s){
                            echo $s['total'].",";
                        }

                    ?>0]
                }],
                tooltip: {
                    theme: 'dark',
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function(seriesName) {
                                return ''
                            }
                        },
                        formatter: (value) => {
                            return '$' + convertNumberWithCommas(value)
                        },
                    },
                    marker: {
                        show: false
                    }
                },
                responsive: [{
                    breakpoint: 1500,
                    options: {
                        chart: {
                            width: 130
                        }
                    }
                }, {
                    breakpoint: 1300,
                    options: {
                        chart: {
                            width: 100
                        }
                    }
                }, {
                    breakpoint: 1200,
                    options: {
                        chart: {
                            width: 200
                        }
                    }
                }, {
                    breakpoint: 576,
                    options: {
                        chart: {
                            width: 180
                        }
                    }
                }, {
                    breakpoint: 400,
                    options: {
                        chart: {
                            width: 120
                        }
                    }
                }]
            };
            if ($('#total-sales-sparkline').length !== 0) {
                new ApexCharts(document.querySelector('#total-sales-sparkline'), options).render();
            }
        };


        var DashboardV3 = function() {
            "use strict";
            return {
                //main function
                init: function() {
                    handleTotalSalesSparkline();
                }
            };
        }();

        $(document).ready(function() {
            DashboardV3.init();
        });
    </script>

<?php
    html::bodyEnd();
});
