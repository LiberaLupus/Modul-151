<?php

namespace controller;


interface ControllerInterface
{
    public function index();
    public function add();
    public function view(int $id);
    public function delete(int $id);
    public function edit(int $id);
}