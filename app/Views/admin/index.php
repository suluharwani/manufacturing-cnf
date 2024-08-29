<?php
echo view('admin/layout/header.php');
echo view('admin/layout/navbar.php');
(isset($content))? $cont = $content: $cont='';
//deklarasi content di controller
echo $cont;
echo view('admin/layout/footer.php');