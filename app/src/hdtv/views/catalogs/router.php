<?php
// ** ## users
dispatch('/catalogs/users/:id/delete', function ($id) {
    require "users/delete.php";
});
dispatch('/catalogs/users', function () {
    require "users/list.php";
});

dispatch('/catalogs/users/:id', function ($id) {
    require "users/form.php";
});
// ** ## profiles
dispatch('/catalogs/profiles', function () {
    require "profiles/list.php";
});

dispatch('/catalogs/profiles/:id', function ($id) {
    require "profiles/form.php";
});
// ** ## warranties
dispatch('/catalogs/warranties', function () {
    require "warranties/list.php";
});

dispatch('/catalogs/warranties/:id/delete', function ($id) {
    require "warranties/delete.php";
});

dispatch('/catalogs/warranties/:id', function ($id) {
    require "warranties/form.php";
});
// ** ## brands
dispatch('/catalogs/brands', function () {
    require "brands/list.php";
});

dispatch('/catalogs/brands/:id/delete', function ($id) {
    require "brands/delete.php";
});
dispatch('/catalogs/brands/:id', function ($id) {
    require "brands/form.php";
});

// ** ## grades
dispatch('/catalogs/grades', function () {
    require "grades/list.php";
});

dispatch('/catalogs/grades/:id/delete', function ($id) {
    require "grades/delete.php";
});
dispatch('/catalogs/grades/:id', function ($id) {
    require "grades/form.php";
});
// ** ## status
dispatch('/catalogs/status', function () {
    require "status/list.php";
});

dispatch('/catalogs/status/:id', function ($id) {
    require "status/form.php";
});
dispatch('/catalogs/status/:id/delete', function ($id) {
    require "status/delete.php";
});
// ** ## payment-methods
dispatch('/catalogs/payment-methods', function () {
    require "payment-methods/list.php";
});

dispatch('/catalogs/payment-methods/:id/delete', function ($id) {
    require "payment-methods/delete.php";
});
dispatch('/catalogs/payment-methods/:id', function ($id) {
    require "payment-methods/form.php";
});
// ** ## bank-accounts
dispatch('/catalogs/bank-accounts', function () {
    require "bank-accounts/list.php";
});

dispatch('/catalogs/bank-accounts/:id/delete', function ($id) {
    require "bank-accounts/delete.php";
});
dispatch('/catalogs/bank-accounts/:id', function ($id) {
    require "bank-accounts/form.php";
});
// ** ## stores
dispatch('/catalogs/stores/:id/delete', function ($id) {
    require "stores/delete.php";
});
dispatch('/catalogs/stores', function () {
    require "stores/list.php";
});

dispatch('/catalogs/stores/:id', function ($id) {
    require "stores/form.php";
});
// ** ## categories
dispatch('/catalogs/categories', function () {
    require "categories/list.php";
});
dispatch('/catalogs/categories/:id/delete', function ($id) {
    require "categories/delete.php";
});
dispatch('/catalogs/categories/:id', function ($id) {
    require "categories/form.php";
});
// ** ## features
dispatch('/catalogs/features', function () {
    require "features/list.php";
});

dispatch('/catalogs/features/:id', function ($id) {
    require "features/form.php";
});
// ** ## price-lists
dispatch('/catalogs/price-lists', function () {
    require "price-lists/list.php";
});

dispatch('/catalogs/price-lists/:id', function ($id) {
    require "price-lists/form.php";
});
// ** ## providers
dispatch('/catalogs/providers', function () {
    require "providers/list.php";
});

dispatch('/catalogs/providers/:id/delete', function ($id) {
    require "providers/delete.php";
});

dispatch('/catalogs/providers/:id', function ($id) {

    require "providers/form.php";
});
// ** ## products
dispatch('/catalogs/products', function () {
    require "products/list.php";
});

dispatch('/catalogs/products/:id', function ($id) {

        require "products/form.php";
});
// ** ## motives
dispatch('/catalogs/motives', function () {
    require "motives/list.php";
});

dispatch('/catalogs/motives/:id', function ($id) {
    if ($id == "export") {
        require "motives/export.php";
    } else {

        require "motives/form.php";
    }
});
// ** ## expenses-types
dispatch('/catalogs/expenses-types', function () {
    require "expenses/list.php";
});

dispatch('/catalogs/expenses-types/:id', function ($id) {


    require "expenses/form.php";
});
