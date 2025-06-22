<?php
session_start();

// إزالة جميع بيانات الجلسة
session_unset();

// تدمير الجلسة بالكامل
session_destroy();

// إعادة توجيه المستخدم إلى صفحة تسجيل الدخول أو الصفحة الرئيسية
header("Location:login.php");
exit();
