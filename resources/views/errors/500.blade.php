@extends('errors.layout')

@section('title', __('site.server_error'))
@section('code', '500')
@section('heading', __('site.server_error'))
@section('message', __('site.server_error_message'))
