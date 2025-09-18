<?php

if (!function_exists('is_verificator')) {
    function is_verificator() {
        $userinfo = userinfo();

        if(!empty($userinfo)){
            return $userinfo->is_verificator;
        }

        return false;
    }
}

if (!function_exists('is_approver')) {
    function is_approver() {
        $userinfo = userinfo();

        if(!empty($userinfo)){
            return $userinfo->is_approver;
        }

        return false;
    }
}