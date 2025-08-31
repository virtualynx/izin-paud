<?php

if (!function_exists('is_verificator')) {
    function is_verificator() {
        return session()->get('is_verificator', false);
    }
}

if (!function_exists('is_approver')) {
    function is_approver() {
        return session()->get('is_approver', false);
    }
}