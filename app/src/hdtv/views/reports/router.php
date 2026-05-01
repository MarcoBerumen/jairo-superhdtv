<?php
dispatch('/reports/attendance', function () {
    include "attendance.php";
});

dispatch('/reports/product-inventory', function () {
    include "inventory.php";
});

dispatch('/reports/sales', function () {
    include "sales.php";
});

dispatch('/reports/comissions', function () {
    include "comissions.php";
});

dispatch('/reports/invoices', function () {
    include "invoices.php";
});
dispatch('/reports/kardex', function () {
    include "kardex.php";
});
dispatch('/reports/webscrapper', function () {
    include "webscrapper.php";
});
dispatch('/reports/pwebscrapperh', function () {
    include "webscrapperh.php";
});

dispatch('/reports/items', function () {
    include "items.php";
});


dispatch('/reports/claims', function () {
    include "claims.php";
});

dispatch('/reports/outgoing-inventory', function () {
    include "out-inventory.php";
});

dispatch('/reports/expenses', function () {
    include "expenses.php";
});

dispatch('/reports/customers', function () {
    include "customers.php";
});

dispatch('/reports/income-statement', function () {
    include "income.php";
});

dispatch('/reports/expiring-soon', function () {
    include "expiring-soon.php";
});
