<?php

// ** ## attendance
dispatch('/back-office/attendance', function () {
    require "attendance/list.php";
});

// ** ## invoices
dispatch('/back-office/invoices', function () {
    require "invoices/list.php";
});

dispatch('/back-office/invoices/:id', function ($id) {
    if ($id == "export") {
        require "invoices/export.php";
    } else {

        require "invoices/form.php";
    }
});
// ** ## outgoing-inventory
dispatch('/back-office/outgoing-inventory', function () {
    require "outgoing-inventory/list.php";
});

dispatch('/back-office/outgoing-inventory/:id', function ($id) {
    if ($id == "export") {
        require "outgoing-inventory/export.php";
    } else {

        require "outgoing-inventory/form.php";
    }
});


dispatch('/back-office/outgoing-inventory/:id/status/:status', function ($id,$status) {
    require "outgoing-inventory/status.php";
});

// ? Payroll
dispatch('/back-office/payroll', function () {
    require "payroll/list.php";
});

dispatch('/back-office/payroll/:id', function ($id) {
        require "payroll/form.php";

});
// ** ## invoices
dispatch('/back-office/inventories', function () {
    require "inventories/list.php";
});

dispatch('/back-office/inventories/:id', function ($id) {
        require "inventories/form.php";
});


dispatch('/back-office/inventories/:id/:status', function ($id,$status) {
    require "inventories/status.php";
});


// ** ## Produc Prices
dispatch('/back-office/product-prices', function () {
    require "product-prices/list.php";
});
dispatch_post('/back-office/product-prices', function () {
    require "product-prices/form.php";
});

// ** ## Sales
dispatch('/back-office/sales', function () {
    require "sales/list.php";
});
dispatch('/back-office/sales/:id/cancel', function ($id) {
    require "sales/cancel.php";
});
dispatch_post('/back-office/sales', function () {
    require "sales/detail.php";
});
// ** ## Claims
dispatch('/back-office/claims', function () {
    require "claims/list.php";
});
dispatch('/back-office/claims/:id', function ($id) {
    require "claims/form.php";
});
dispatch('/back-office/claims/:id/status/:status', function ($id,$status) {
    require "claims/status.php";
});
dispatch('/back-office/claims/:id/print', function ($id) {
    require "claims/print.php";
});
dispatch('/public/claims/:id/print', function ($id) {
    require "claims/print.php";
});
// ** ## customers
dispatch('/back-office/customers', function () {
    require "customers/list.php";
});
dispatch('/back-office/customers/:id/delete', function ($id) {
    require "customers/delete.php";
});


dispatch('/back-office/customers/:id', function ($id) {
    require "customers/form.php";
});

// ! expenses
dispatch('/back-office/expenses', function () {
    require "expenses/list.php";
});
dispatch('/back-office/expenses/:id', function ($id) {
    require "expenses/form.php";
});
dispatch('/back-office/expenses/:id/delete', function ($id) {
    require "expenses/delete.php";
});


// ! TRANSFERS
dispatch('/back-office/transfers', function () {
    require "transfers/list.php";
});
dispatch('/back-office/transfers/:id', function ($id) {
    require "transfers/form.php";
});

dispatch('/back-office/transfers/:id/status/:$status', function ($id,$status) {
    require "transfers/status.php";
});
