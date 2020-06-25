<?php


namespace Uploadcare\Interfaces;

interface UploadcareApiInterface
{
    public function getFile();

    public function request();

    public function getFileList();

    public function getGroupList();

    public function getGroup();
}
