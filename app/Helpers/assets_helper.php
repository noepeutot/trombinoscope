<?php

if ( ! function_exists('css_url'))
{
    function css_url($nom): string
    {
        return base_url() . 'assets/CSS/' . $nom . '.css';
    }
}

if ( ! function_exists('js_url'))
{
    function js_url($nom): string
    {
        return base_url() . 'assets/JS/' . $nom . '.js';
    }
}

if ( ! function_exists('img_url'))
{
    function img_url($nom): string
    {
        return base_url() . 'assets/images/' . $nom;
    }
}

if ( ! function_exists('img'))
{
    function img($nom, $alt = ''): string
    {
        return '<img src="' . img_url($nom) . '" alt="' . $alt . '" />';
    }
}