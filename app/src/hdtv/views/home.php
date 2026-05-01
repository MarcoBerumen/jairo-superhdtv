<?php

use Imx\html2;

html2::head("Dashboard");
html2::bodyInit();
html2::header("");
html2::sidebar();
html2::beginContent();
$user = Imx\db::dataQuery("select
     users.user_id,
     users.name
     from users 
     where token ='{$token}'
     ");
$store = hdtv::storeFilter()??0;
//exit;
//echo $store;
$start_date = $_GET['start']??date('Y-m-01')." 00:00:00";
$end_date = $_GET['end']??date('Y-m-d')." 23:59:59";
//$end_date = date('Y-m-d 23:59:59');
$start_datef = date('Y-m-01 ');
$end_datef = date('Y-m-d');
$sales = Imx\db::rquery("select sum(subtotal) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store)") ?? 0;
$salescnt = Imx\db::rquery("select count(*) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store)") ?? 0;


$salesvg = Imx\utils::decimales2(Imx\db::rquery("select avg(total) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store)") ?? 0);
$usersales = Imx\db::dataQueryMultiple("select sum(subtotal) as total,view_sales.user,users.face from users left join view_sales on view_sales.user_id = users.user_id  where users.store_id in ({$store}) and view_sales.store_id in ({$store}) and date between '{$start_date}' and '{$end_date}' and view_sales.status_code = 1 group by users.user_id");

$top10 = Imx\db::dataQueryMultiple("select
    count(transactions.quantity) as sales,
    view_items.product,
    products.images

    from  transactions
              left join view_items on view_items.item_id = transactions.item_id
    left join products on products.product_id = view_items.product_id

                  where reference_id in

                            (
select sale_id from sales where 
                               date between '{$start_date}' and '{$end_date}' and store_id in($store)
                               and status = 1
                            )

and transactions.transaction_type ='Sale'
group by view_items.product_id
order by count(transactions.quantity) desc limit 10
");

$purchases = Imx\db::rquery("select sum(total_price) as total from view_invoices where date between date('{$start_date}') and date('{$end_date}')  ");
$purchasespp = Imx\db::dataQueryMultiple("select sum(total_price) as total , provider from view_invoices where date between date('{$start_date}') and date('{$end_date}')   group by provider ");
$customers = Imx\db::rquery("select count(*) as total from customers where member_since between date('{$start_date}') and date('{$end_date}')  ");
$salesnew  = Imx\db::rquery("select sum(subtotal) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store) and customer_id in( select customer_id from customers where member_since between date('{$start_date}') and date('{$end_date}') )") ?? 0;
$salesreturning  = Imx\db::rquery("select sum(subtotal) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store) and customer_id not in( select customer_id from customers where member_since between date('{$start_date}') and date('{$end_date}') )") ?? 0;
if($sales){
$avgnew = intval(($salesnew/$sales)*100);
$avgreturning = intval(($salesreturning/$sales)*100);

}
else{
    $avgnew = 0;
    $avgreturning = 0;
}
$sales = Imx\utils::decimales2(Imx\db::rquery("select sum(subtotal) from sales 
    where  status =1 and date between '{$start_date}' and '{$end_date}' and store_id in($store)") ?? 0);

?>
<link href="/assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
<link href="/assets/plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
<!-- ================== END page-css ================== -->

<ol class="breadcrumb float-xl-end">
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Dashboard</a></li>
</ol>
<!-- END breadcrumb -->
<!-- BEGIN page-header -->
<!-- END page-header -->
<!-- BEGIN daterange-filter -->
<div class="d-sm-flex align-items-center mb-3">
    <a href="#" class="btn btn-dark me-2 text-truncate" id="daterange-filter">
        <i class="fa fa-calendar fa-fw text-white text-opacity-50 ms-n1"></i>
        <span></span>
        <b class="caret ms-1 opacity-5"></b>
    </a>
</div>
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
                        <!-- BEGIN title --><div class="mb-3 text-gray-500">
                            <b>TOTAL SALES</b>
                            <span class="ms-2">
                                <i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Total sales" data-bs-placement="top" data-bs-content="Sum of all invoices"></i>
                            </span>
                        </div>
                        <!-- END title -->
                        <!-- BEGIN total-sales -->
                        <div class="d-flex mb-1">
                            <h2 class="mb-0">$<span data-animation="number" data-value="<?php echo $sales;?>">0</span></h2>
<!--                            <div class="ms-auto mt-n1 mb-n1">-->
<!--                                <div id="total-sales-sparkline"></div>-->
<!--                            </div>-->
                        </div>
                        <!-- END total-sales -->
                        <!-- BEGIN percentage -->

                        <!-- END percentage -->
                        <hr class="bg-white bg-opacity-50" />
                        <!-- BEGIN row -->
                        <div class="row text-truncate">
                            <!-- BEGIN col-6 -->
                            <div class="col-6">
                                <div class=" text-gray-500">Total sales</div>
                                <div class="fs-18px mb-5px fw-bold" data-animation="number" data-value="<?php echo $salescnt;?>">0</div>

                            </div>
                            <!-- END col-6 -->
                            <!-- BEGIN col-6 -->
                            <div class="col-6">
                                <div class=" text-gray-500">Avg. sales per order</div>
                                <div class="fs-18px mb-5px fw-bold">$<span data-animation="number" data-value="<?php echo $salesvg;?>">0.00</span></div>
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
    <div class="col-xl-6">
        <!-- BEGIN row -->
        <div class="row">
            <!-- BEGIN col-6 -->
            <div class="col-sm-6">
                <!-- BEGIN card -->
                <div class="card border-0 text-truncate mb-3 bg-gray-800 text-white">
                    <!-- BEGIN card-body -->
                    <div class="card-body">
                        <!-- BEGIN title -->
                        <div class="mb-3 text-gray-500">
                            <b class="mb-3">Customers</b>
                            <span class="ms-2"><i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Conversion Rate" data-bs-placement="top" data-bs-content="Percentage of sessions that resulted in orders from total number of sessions." data-original-title="" title=""></i></span>
                        </div>
                        <!-- END title -->
                        <!-- BEGIN conversion-rate -->
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="text-white mb-0"><span data-animation="number" data-value="<?php echo $customers;?>">0.00</span>+</h2>
<!--                            <div class="ms-auto">-->
<!--                                <div id="conversion-rate-sparkline"></div>-->
<!--                            </div>-->
                        </div>
                        <!-- END conversion-rate -->
                        <!-- BEGIN percentage -->
                        <div class="mb-4 text-gray-500 ">
<!--                            <i class="fa fa-caret-down"></i> <span data-animation="number" data-value="0.50">0.00</span>% compare to last week-->
                        </div>
                        <!-- END percentage -->
                        <!-- BEGIN info-row -->

                        <!-- END info-row -->
                        <!-- BEGIN info-row -->
                        <div class="d-flex mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-warning fs-8px me-2"></i>
                               Sales New customer
                            </div>
                            <div class="d-flex align-items-center ms-auto">
                                <div class="text-gray-500 small"><i class="fa fa-caret-up"></i> <span data-animation="number" data-value="<?php echo $salesnew;?>">0</span></div>
                                <div class="w-50px text-end ps-2 fw-bold"><span data-animation="number" data-value="<?php echo $avgnew;?>">0.00</span>%</div>
                            </div>
                        </div>
                        <!-- END info-row -->
                        <!-- BEGIN info-row -->
                        <div class="d-flex">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-lime fs-8px me-2"></i>
                                Sales Returning Customer
                            </div>
                            <div class="d-flex align-items-center ms-auto">
                                <div class="text-gray-500 small"><i class="fa fa-caret-up"></i> <span data-animation="number" data-value="<?php echo $salesreturning;?>">0</span></div>
                                <div class="w-50px text-end ps-2 fw-bold"><span data-animation="number" data-value="<?php echo $avgreturning;?>">0.00</span>%</div>
                            </div>
                        </div>
                        <!-- END info-row -->
                    </div>
                    <!-- END card-body -->
                </div>
                <!-- END card -->
            </div>
            <!-- END col-6 -->
            <!-- BEGIN col-6 -->
            <div class="col-sm-6">
                <!-- BEGIN card -->
                <div class="card border-0 text-truncate mb-3 bg-gray-800 text-white">
                    <!-- BEGIN card-body -->
                    <div class="card-body">
                        <!-- BEGIN title -->
                        <div class="mb-3 text-gray-500">
                            <b class="mb-3">PURCHASES</b>
                            <span class="ms-2"><i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Providers Purchases" data-bs-placement="top" data-bs-content="Sum of invoices total withing selected period" data-original-title="" title=""></i></span>
                        </div>
                        <!-- END title -->
                        <!-- BEGIN store-session -->
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="text-white mb-0"><span data-animation="number" data-value="<?php echo $purchases;?>">0</span></h2>
<!--                            <div class="ms-auto">-->
<!--                                <div id="store-session-sparkline"></div>-->
<!--                            </div>-->
                        </div>
                        <!-- END store-session -->
                        <!-- BEGIN percentage -->
<!--                        <div class="mb-4 text-gray-500 ">-->
<!--                            <i class="fa fa-caret-up"></i> <span data-animation="number" data-value="9.5">0.00</span>-->
<!--                        </div>-->
                        <!-- END percentage -->
                        <!-- BEGIN info-row -->
                        <?php
                        $colors = ["teal","blue","cyan"];
                        foreach($purchasespp as $p)
                        {
                            $color = array_pop($colors);
                            ?>
                            <div class="d-flex mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle text-<?php echo $color;?> fs-8px me-2"></i>
                                    <?php echo $p['provider'];?>
                                </div>
                                <div class="d-flex align-items-center ms-auto">
<!--                                    <div class="text-gray-500 small"><i class="fa fa-caret-up"></i> <span data-animation="number" data-value="25.7">0.00</span>%</div>-->
                                    <div class="w-50px text-end ps-2 fw-bold"><span data-animation="number" data-value="<?php echo $p['total'];?>">0</span></div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- END info-row -->

                        <!-- END info-row -->
                    </div>
                    <!-- END card-body -->
                </div>
                <!-- END card -->
            </div>
            <!-- END col-6 -->
        </div>
        <!-- END row -->
    </div>
    <!-- END col-6 -->
</div>
<!-- END row -->
<!-- BEGIN row -->
<div class="row">
    <!-- BEGIN col-8 -->
    <div class="col-12">
        <!-- BEGIN card -->
        <div class="card border-0 mb-3 bg-gray-800 text-white">
            <div class="card-body">
                <div class="mb-3 text-gray-500 "><b>SALES DURING THIS PERIOD</b> <span class="ms-2"><i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Top products with units sold" data-bs-placement="top" data-bs-content="Products with the most individual units sold. Includes orders from all sales channels." data-original-title="" title=""></i></span></div>

            </div>
            <div class="card-body p-0">
                <div style="height: 269px">
                    <div id="sales-line-chart" class="widget-chart-full-width dark-mode" style="height: 254px"></div>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>
    <!-- END col-8 -->
    <!-- BEGIN col-4 -->

    <!-- END col-4 -->
</div>
<!-- END row -->
<!-- BEGIN row -->
<div class="row">
    <!-- BEGIN col-4 -->
    <div class="col-xl-4 col-lg-6">
        <!-- BEGIN card -->
        <div class="card border-0 mb-3 bg-gray-900 text-white">
            <!-- BEGIN card-body -->
            <div class="card-body" style="background: no-repeat bottom right; background-image: url(../assets/img/svg/img-4.svg); background-size: auto 60%;">
                <!-- BEGIN title -->
                <div class="mb-3 text-gray-500 ">
                    <b>SALES BY AGENT</b>
                    <span class="text-gray-500 ms-2"><i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Sales by social source" data-bs-placement="top" data-bs-content="Total Sales per user"></i></span>
                </div>
                <!-- END title -->
                <!-- BEGIN sales -->
                <h3 class="mb-10px">$<span data-animation="number" data-value="<?php echo $sales;?>">0.00</span></h3>
                <!-- END sales -->
                <!-- BEGIN percentage -->
                <div class="text-gray-500 mb-1px"><i class="fa fa-caret-up"></i> <span data-animation="number" data-value="45.76">0.00</span>% increased</div>
                <!-- END percentage -->
            </div>
            <!-- END card-body -->
            <!-- BEGIN widget-list -->
            <div class="widget-list rounded-bottom dark-mode">
                <?php
                foreach($usersales as $sale){

                    ?>
                    <!-- BEGIN widget-list-item -->
                    <a href="#" class="widget-list-item rounded-0 pt-3px">
                        <div class="widget-img rounded-3 me-10px bg-white p-3px w-30px">
                            <div class="h-100 w-100" style="background: url('/api/s3/<?php echo $sale['face'];?>?mini=1') center no-repeat; background-size: auto 100%;"></div>
                        </div>


                        <div class="widget-list-content">
                            <div class="widget-list-title"><?php echo $sale['user'];?></div>
                        </div>
                        <div class="widget-list-action text-nowrap text-gray-500">
                            $<span data-animation="number" data-value="<?php echo $sale['total'];?>">0.00</span>
                        </div>
                    </a>
                    <?php
                }
                ?>
                <!-- END widget-list-item -->
            </div>
            <!-- END widget-list -->
        </div>
        <!-- END card -->
    </div>



    <!-- END col-4 -->
    <!-- END col-4 -->
    <!-- BEGIN col-4 -->
    <div class="col-xl-4 col-lg-4 col-12">
        <!-- BEGIN card -->
        <div class="card border-0 mb-3 bg-gray-800 text-white">
            <!-- BEGIN card-body -->
            <div class="card-body">
                <!-- BEGIN title -->
                <div class="mb-3 text-gray-500">
                    <b>TOP 10 PRODUCTS</b>
                    <span class="ms-2 "><i class="fa fa-info-circle" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-title="Top products with units sold" data-bs-placement="top" data-bs-content="Products with the most individual units sold. Includes orders from all sales channels."></i></span>
                </div>
                <!-- END title -->
                <!-- BEGIN product -->
                <?php
                foreach($top10 as $product){
                    $img = $product['images'];
//                    echo $img;
//                $img = explode($img,",");
//                $img = $img[0];

                ?>
                <div class="d-flex align-items-center mb-15px">
                    <div class="widget-img rounded-3 me-10px bg-white p-3px w-30px">
                        <div class="h-100 w-100" style="background: url('/api/s3/<?php echo $img;?>?mini=1') center no-repeat; background-size: auto 100%;"></div>
                    </div>
                    <div class="text-truncate">
                        <div><?php echo $product['product'];?></div>
                        <div class="text-gray-500"></div>
                    </div>
                    <div class="ms-auto text-center">
                        <div class="fs-13px"><span data-animation="number" data-value="<?php echo $product['sales'];?>">0</span></div>
                        <div class="text-gray-500 fs-10px"> sold</div>
                    </div>
                </div>
                <!-- END product -->
                <?php
                }
                ?>

            </div>
            <!-- END card-body -->
        </div>
        <!-- END card -->
    </div>
    <!-- END col-4 -->
    <!-- BEGIN col-4 -->
    <!-- END col-4 -->
</div>
<!-- END row -->

<?php


html2::endContent();
html2::containerEnd();
html2::scripts();
?>
<script src="/assets2/plugins/d3/d3.min.js"></script>
<script src="/assets2/plugins/nvd3/build/nv.d3.min.js"></script>
<script src="/assets2/plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
<script src="/assets2/plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
<script src="/assets2/plugins/apexcharts/dist/apexcharts.min.js"></script>
<script src="/assets2/plugins/moment/min/moment.min.js"></script>
    <script src="/assets2/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script>
    /*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 5
Version: 5.1.3
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin/
*/

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
                        color: app.color.blue,
                        opacity: 1
                    },
                        {
                            offset: 100,
                            color: app.color.indigo,
                            opacity: 1
                        }]
                },
            },
            series: [{
                data: [9452.37, 11018.87, 7296.37, 6274.29, 7924.05, 6581.34, 12918.14]
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
                        formatter: function (seriesName) {
                            return ''
                        }
                    },
                    formatter: (value) => { return '$'+ convertNumberWithCommas(value) },
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
            },{
                breakpoint: 1300,
                options: {
                    chart: {
                        width: 100
                    }
                }
            },{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 200
                    }
                }
            },{
                breakpoint: 576,
                options: {
                    chart: {
                        width: 180
                    }
                }
            },{
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

    var handleConversionRateSparkline = function() {
        var options = {
            chart: {
                type: 'line',
                width: 160,
                height: 28,
                sparkline: {
                    enabled: true
                }
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
                        color: app.color.red,
                        opacity: 1
                    },
                        {
                            offset: 50,
                            color: app.color.orange,
                            opacity: 1
                        },
                        {
                            offset: 100,
                            color: app.color.lime,
                            opacity: 1
                        }]
                },
            },
            series: [{
                data: [2.68, 2.93, 2.04, 1.61, 1.88, 1.62, 2.80]
            }],
            labels: ['Jun 23', 'Jun 24', 'Jun 25', 'Jun 26', 'Jun 27', 'Jun 28', 'Jun 29'],
            xaxis: {
                crosshairs: {
                    width: 1
                },
            },
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
                        formatter: function (seriesName) {
                            return ''
                        }
                    },
                    formatter: (value) => { return value + '%' },
                },
                marker: {
                    show: false
                }
            },
            responsive: [{
                breakpoint: 1500,
                options: {
                    chart: {
                        width: 120
                    }
                }
            },{
                breakpoint: 1300,
                options: {
                    chart: {
                        width: 100
                    }
                }
            },{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 160
                    }
                }
            },{
                breakpoint: 900,
                options: {
                    chart: {
                        width: 120
                    }
                }
            },{
                breakpoint: 576,
                options: {
                    chart: {
                        width: 180
                    }
                }
            },{
                breakpoint: 400,
                options: {
                    chart: {
                        width: 120
                    }
                }
            }]
        }
        if ($('#conversion-rate-sparkline').length !== 0) {
            new ApexCharts(document.querySelector("#conversion-rate-sparkline"), options).render();
        }
    };

    var handleStoreSessionSparkline = function() {
        var options = {
            chart: {
                type: 'line',
                width: 160,
                height: 28,
                sparkline: {
                    enabled: true
                },
                stacked: false
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
                        color: app.color.teal,
                        opacity: 1
                    },
                        {
                            offset: 50,
                            color: app.color.blue,
                            opacity: 1
                        },
                        {
                            offset: 100,
                            color: app.color.cyan,
                            opacity: 1
                        }]
                },
            },
            series: [{
                data: [10812, 11393, 7311, 6834, 9612, 11200, 13557]
            }],
            labels: ['Jun 23', 'Jun 24', 'Jun 25', 'Jun 26', 'Jun 27', 'Jun 28', 'Jun 29'],
            xaxis: {
                crosshairs: {
                    width: 1
                },
            },
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
                        formatter: function (seriesName) {
                            return ''
                        }
                    },
                    formatter: (value) => { return convertNumberWithCommas(value) },
                },
                marker: {
                    show: false
                }
            },
            responsive: [{
                breakpoint: 1500,
                options: {
                    chart: {
                        width: 120
                    }
                }
            },{
                breakpoint: 1300,
                options: {
                    chart: {
                        width: 100
                    }
                }
            },{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 160
                    }
                }
            },{
                breakpoint: 900,
                options: {
                    chart: {
                        width: 120
                    }
                }
            },{
                breakpoint: 576,
                options: {
                    chart: {
                        width: 180
                    }
                }
            },{
                breakpoint: 400,
                options: {
                    chart: {
                        width: 120
                    }
                }
            }]
        };
        if ($('#store-session-sparkline').length !== 0) {
            new ApexCharts(document.querySelector('#store-session-sparkline'), options).render();
        }
    };
    <?php
    $end_date = $_GET['end']??date('Y-m-01');

    ?>
    var handleVisitorsAreaChart = function() {
        var handleGetDate = function(minusDate) {
            var d = new Date('<?php echo $end_date;?> 16:00:00');
            d = d.setDate(d.getDate() - minusDate);
            return d;
        };
        <?php
//        $end_date = date('Y-m-01');
        // End date
//        $date = date('Y-m-d');
        $end_date = $_GET['start']??date('Y-m-01');

        $salesdate = [];
        $date = $_GET['end']??date('Y-m-d');
        while (strtotime($date) >= strtotime($end_date) ) {
            $q ="select sum(subtotal) from sales 
            where  status =1 and date(date)  = '{$date}' and store_id in($store)";
            $salesdate[]= Imx\db::rquery($q);
            $date = date ("Y-m-d", strtotime("-1 day", strtotime($date)));
        }
//        print_r($salesdate);
        ?>
        var salesAreaChartData = [{
            'key' : 'Sales per day',
            'color' : app.color.cyan,
            'values' : [
                <?php
                foreach($salesdate as $key=>$value){
                    echo "[handleGetDate({$key}), $value],";
                }
                ?>

            ]
        }
        ];
        if ($('#sales-line-chart').length !== 0) {
            nv.addGraph(function() {
                var stackedAreaChart = nv.models.stackedAreaChart()
                    .useInteractiveGuideline(true)
                    .x(function(d) { return d[0] })
                    .y(function(d) { return d[1] })
                    .pointSize(0.5)
                    .margin({'left':35,'right': 25,'top': 20,'bottom':20})
                    .controlLabels({stacked: 'Stacked'})
                    .showControls(false)
                    .duration(300);

                stackedAreaChart.xAxis.tickFormat(function(d) {
                    // console.log(d);
                    var monthsName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    d = new Date(d);
                    d = monthsName[d.getMonth()] + ' ' + d.getDate();
                    return d ;
                });
                stackedAreaChart.yAxis.tickFormat(d3.format(',.0f'));

                d3.select('#sales-line-chart')
                    .append('svg')
                    .datum(salesAreaChartData)
                    .transition().duration(1000)
                    .call(stackedAreaChart)
                    .each('start', function() {
                        setTimeout(function() {
                            d3.selectAll('#sales-line-chart *').each(function() {
                                if(this.__transition__)
                                    this.__transition__.duration = 1;
                            })
                        }, 0)
                    });

                nv.utils.windowResize(stackedAreaChart.update);
                return stackedAreaChart;
            });
        }
    };

    var handleVisitorsMap = function() {
        var fillColor = ($('#visitors-map').attr('data-theme')) ? 'rgba('+ app.color.componentColorRgb +', .25)' : app.color.gray700;
        var options = {
            map: 'world_mill',
            scaleColors: [app.color.black, app.color.black],
            container: $('#visitors-map'),
            normalizeFunction: 'linear',
            hoverOpacity: 0.5,
            hoverColor: false,
            zoomOnScroll: false,
            zoomButtons: false,
            markerStyle: {
                initial: {
                    fill: app.color.black,
                    stroke: 'transparent',
                    r: 3
                }
            },
            regions: [{
                attribute: 'fill'
            }],
            regionStyle: {
                initial: {
                    fill: fillColor,
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 0.4,
                    "stroke-opacity": 1
                },
                hover: {
                    "fill-opacity": 0.8
                },
                selected: {
                    fill: 'yellow'
                }
            },
            series: {
                regions: [{
                    values: {
                        IN: app.color.teal,
                        US: app.color.teal,
                        MN: app.color.teal,
                        RU: app.color.teal
                    }
                }]
            },
            focusOn: {
                x: 0.7,
                y: 0.5,
                scale: 1
            },
            backgroundColor: 'transparent'
        };
        if ($('#visitors-map').length !== 0) {
            $('#visitors-map').vectorMap(options);
        }
    }

    var handleDateRangeFilter = function() {
        <?php
        $start_date = $_GET['start']??date('Y-m-01');
        $end_date = $_GET['end']??date('Y-m-d');

        ?>
        $('#daterange-filter span').html(moment("<?php echo $start_date;?>",'YYYY-MM-DD').format('D MMMM YYYY') + ' - ' + moment("<?php echo $end_date;?>",'YYYY-MM-DD').format('D MMMM YYYY'));

        $('#daterange-filter').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment("<?php echo $start_date;?>",'YYYY-MM-DD'),
            endDate: moment("<?php echo $end_date;?>",'YYYY-MM-DD'),
            //minDate: '<?php //echo date('m/01/Y');?>//',
            //maxDate: '<?php //echo date('m/d/Y');?>//',
            dateLimit: { days: 60 },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'right',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function(start, end, label) {
            $('#daterange-filter span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            window.location.href='/dashboard?start='+start.format('YYYY-MM-DD') +'&end='+end.format('YYYY-MM-DD');
            var gap = end.diff(start, 'days');
        });
    };

    var DashboardV3 = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleTotalSalesSparkline();
                handleConversionRateSparkline();
                handleStoreSessionSparkline();
                handleVisitorsAreaChart();
                handleVisitorsMap();
                handleDateRangeFilter();
            }
        };
    }();

    $(document).ready(function() {
        DashboardV3.init();
    });

</script>
<?php
html2::bodyEnd();
