<?php



$_SESSION['menu'] =
    [
        [
            "module" => "Dashboard",
            "name" => "dashboard",
            "href" => "/dashboard",
            "icon" => "chart",
            "sub" => false
        ],

        [
            "module" => "Back Office",
            "name" => "back-office",
            "href" => "javascript:;",
            "icon" => "building",
            "sub" => true,
            "items" => [
                [
                    "module" => "Attendance",
                    "name" => "attendance",
                    "href" => "/back-office/attendance",
                    "icon" => "icon",
                ],
                [
                    "module" => "Claims",
                    "name" => "claims",
                    "href" => "/back-office/claims",
                    "icon" => "icon",

                ],
                [
                    "module" => "Customers",
                    "name" => "customers",
                    "href" => "/back-office/customers",
                    "icon" => "person",
                ],
                [
                    "module" => "Expenses",
                    "name" => "expenses",
                    "href" => "/back-office/expenses",
                    "icon" => "icon",
                ],
                [
                    "module" => "Invoices",
                    "name" => "invoices",
                    "href" => "/back-office/invoices",
                    "icon" => "fa-note",
                ],


                [
                    "module" => "Outgoing Inventory",
                    "name" => "outgoing-inventory",
                    "href" => "/back-office/outgoing-inventory",
                    "icon" => "icon",
                ],
                [
                    "module" => "Payroll",
                    "name" => "payrol",
                    "href" => "/back-office/payroll",
                    "icon" => "icon",
                ],

                [
                    "module" => "Physical inventory",
                    "name" => "inventories",
                    "href" => "/back-office/inventories",
                    "icon" => "icon",
                ],
                [
                    "module" => "Products Prices",
                    "name" => "product-prices",
                    "href" => "/back-office/product-prices",
                    "icon" => "icon",
                ],
                [
                    "module" => "Sales",
                    "name" => "sales",
                    "href" => "/back-office/sales",
                    "icon" => "icon",
                ],
                [
                    "module" => "Transfers",
                    "name" => "transfers",
                    "href" => "/back-office/transfers",
                    "icon" => "icon",
                ],
            ]
        ],

        [

            "module" => "Catalogs",
            "name" => "catalogs",
            "href" => "javascript:;",
            "icon" => "th-list",
            "sub" => true,
            "items" => [
                [
                    "module" => "Bank Accounts",
                    "name" => "bankaccounts",
                    "href" => "/catalogs/bank-accounts",
                    "icon" => "icon",
                ],
                [
                    "module" => "Brands",
                    "name" => "brands",
                    "href" => "/catalogs/brands",
                    "icon" => "icon",
                ],
                [
                    "module" => "Categories",
                    "name" => "categories",
                    "href" => "/catalogs/categories",
                    "icon" => "icon",
                ],
                [
                    "module" => "Expenses types",
                    "name" => "expensestypes",
                    "href" => "/catalogs/expenses-types",
                    "icon" => "icon",
                ],
                [
                    "module" => "Features",
                    "name" => "features",
                    "href" => "/catalogs/features",
                    "icon" => "icon",
                ],
                [
                    "module" => "Grades",
                    "name" => "grades",
                    "href" => "/catalogs/grades",
                    "icon" => "icon",
                ],
                [
                    "module" => "Outgoing Inventory Motives",
                    "name" => "motives",
                    "href" => "/catalogs/motives",
                    "icon" => "icon",
                ],
                [
                    "module" => "Payment methods",
                    "name" => "paymentmethods",
                    "href" => "/catalogs/payment-methods",
                    "icon" => "icon",
                ],
                [
                    "module" => "Profiles",
                    "name" => "profiles",
                    "href" => "/catalogs/profiles",
                    "icon" => "icon",
                ],
                [
                    "module" => "Providers",
                    "name" => "providers",
                    "href" => "/catalogs/providers",
                    "icon" => "icon",
                ],
                [
                    "module" => "Price lists",
                    "name" => "price-lists",
                    "href" => "/catalogs/price-lists",
                    "icon" => "icon",
                ],
                [
                    "module" => "Products",
                    "name" => "products",
                    "href" => "/catalogs/products",
                    "icon" => "icon",
                ],
                [
                    "module" => "Stores",
                    "name" => "stores",
                    "href" => "/catalogs/stores",
                    "icon" => "icon",
                ],
                [
                    "module" => "Status",
                    "name" => "status",
                    "href" => "/catalogs/status",
                    "icon" => "icon",
                ],
                [
                    "module" => "Users",
                    "name" => "users",
                    "href" => "/catalogs/users",
                    "icon" => "icon",
                ],
                [
                    "module" => "Warranties",
                    "name" => "warranties",
                    "href" => "/catalogs/warranties",
                    "icon" => "icon",
                ],

                // [
                //     "module" => "Comissions",
                //     "name" => "comissions",
                //     "href" => "/catalogs/comissions",
                //     "icon" => "icon",
                // ],



            ]
        ],
        [
            "module" => "Reports",
            "name" => "reports",
            "href" => "javascript:;",
            "icon" => "file-alt",
            "sub" => true,
            "items" => [
                [
                    "module" => "Attendance",
                    "name" => "attendance",
                    "href" => "/reports/attendance",
                    "icon" => "icon",
                ],
                [
                    "module" => "Claims",
                    "name" => "claims",
                    "href" => "/reports/claims",
                    "icon" => "icon",
                ],
                [
                    "module" => "Comissions",
                    "name" => "comissions",
                    "href" => "/reports/comissions",
                    "icon" => "icon",
                ],
                [
                    "module" => "Customers",
                    "name" => "customers",
                    "href" => "/reports/customers",
                    "icon" => "icon",
                ],
                [
                    "module" => "Expenses",
                    "name" => "expenses",
                    "href" => "/reports/expenses",
                    "icon" => "icon",
                ],
                [
                    "module" => "Expiring Soom Items",
                    "name" => "expiring-soom",
                    "href" => "/reports/expiring-soon",
                    "icon" => "icon",
                ],
                [
                    "module" => "Invoices",
                    "name" => "invoices",
                    "href" => "/reports/invoices",
                    "icon" => "icon",
                ],
                [
                    "module" => "Income Statement",
                    "name" => "income-statement",
                    "href" => "/reports/income-statement",
                    "icon" => "fa-note",
                ],
                [
                    "module" => "Items",
                    "name" => "items",
                    "href" => "/reports/items",
                    "icon" => "icon",
                ],
                [
                    "module" => "Outgoing Inventory",
                    "name" => "outinventory",
                    "href" => "/reports/outgoing-inventory",
                    "icon" => "icon",
                ],
                [
                    "module" => "Product Inventory",
                    "name" => "productinventory",
                    "href" => "/reports/product-inventory",
                    "icon" => "icon",
                ],
                [
                    "module" => "Product Kardex",
                    "name" => "productkardex",
                    "href" => "/reports/kardex",
                    "icon" => "icon",
                ],
                [
                    "module" => "Sales",
                    "name" => "sales",
                    "href" => "/reports/sales",
                    "icon" => "icon",
                ],
                [
                    "module" => "Web Scraper",
                    "name" => "webscrapper",
                    "href" => "/reports/webscrapper",
                    "icon" => "icon",
                ],
                [
                    "module" => "Web Scraper History",
                    "name" => "pwebscrapperh",
                    "href" => "/reports/pwebscrapperh",
                    "icon" => "icon",
                ],

            ]
        ],
        [
            "module" => "System Settings",
            "name" => "system-settings",
            "href" => "/system-settings",
            "icon" => "cog",
            "sub" => false
        ],
        [
            "module" => "Mobile POS",
            "icon" => "tablet-button",
            "name" => "pos",
            "href" => "/pos",
            "sub" => false
        ],
        [
            "module" => "Webscraper",
            "name" => "webscrapper",
            "href" => "/files/webscraper.exe",
            "icon" => "file-download",
            "sub" => false
        ],
        [
            "module" => "Logout",
            "name" => "logout",
            "href" => "/logout",
            "icon" => "door-open",
            "sub" => false
        ],
    ];
$permissions = json_decode(Imx\db::rquery("select permissions from profiles where profile_id ='{$_SESSION['user']['profile_id']}'"),true);

foreach($_SESSION['menu'] as &$item){
    $parts = explode("/", $_SERVER['REQUEST_URI']);

    if ($parts[0] == "")
        $parts = array_splice($parts, 1);

        if ($item['sub']) {
            if ($item['name'] == $parts[0])
            {
                $item['active'] = 'active';
            }

            foreach ($item['items'] as &$sub) {
                $active = "";
                if ($sub['sub'] ?? "" != "") {
                    if ($sub['name']  == $parts[1] ?? "" && $parts[1] ?? "" != "") {
                        $sub['active'] = 'active';
                    }

                } else {
                    if (strpos($_SERVER['REQUEST_URI'], $sub['href']) !== false)
                    $sub['active'] = 'active';
                }

                }


        } else {

            $active =   "";
            if ($item['name'] == $parts[0])
                $item['active'] = 'active';

        }


    if(isset($permissions[$item['name']])) {
        $item['enabled'] = true;
        if ($item['sub']) {
            foreach ($item['items'] as &$sub) {

                if(isset($permissions[$item['name']][$sub['name']])){
                    $sub['enabled'] = true;
                }
                else{
                    $sub['enabled'] = false;
                }
            }
            }
            }
    else{
        $item['enabled'] = false;
    }

    }
