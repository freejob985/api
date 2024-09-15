<?php
// تحديد المسار الأساسي للتطبيق
$app_path = '/api';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$app_path";
$api_base = $base_url . "/api";

// التحقق من وجود طلب API وإعادة توجيهه
if (strpos($_SERVER['REQUEST_URI'], $app_path . '/api') === 0) {
    // إزالة $app_path من بداية URI
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($app_path));
    require_once 'api.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>توثيق API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .endpoint {
            background-color: #ecf0f1;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .endpoint:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .endpoint h3 {
            color: #34495e;
            font-weight: 600;
        }
        .method {
            font-weight: 600;
            color: #27ae60;
        }
        .url {
            background-color: #f9f9f9;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            padding: 8px 12px;
            margin-right: 10px;
            word-break: break-all;
        }
        .copy-btn {
            cursor: pointer;
            color: #3498db;
            transition: color 0.3s;
        }
        .copy-btn:hover {
            color: #2980b9;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
        }
        .btn-success {
            background-color: #2ecc71;
            border-color: #2ecc71;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">توثيق API</h1>
        
        <h2 class="mb-4">المنشورات</h2>
        
        <div class="endpoint">
            <h3><span class="material-icons">list</span> الحصول على جميع المنشورات</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="get-all-posts"><?php echo $api_base; ?>/posts</code> <span class="material-icons copy-btn" onclick="copyToClipboard('get-all-posts')">content_copy</span></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">find_in_page</span> الحصول على منشور واحد</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="get-one-post"><?php echo $api_base; ?>/posts/1</code> <span class="material-icons copy-btn" onclick="copyToClipboard('get-one-post')">content_copy</span></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">add_circle</span> إنشاء منشور جديد</h3>
            <p><span class="method">الطريقة:</span> POST</p>
            <p><span class="method">الرابط:</span> <code class="url" id="create-post"><?php echo $api_base; ?>/posts</code> <span class="material-icons copy-btn" onclick="copyToClipboard('create-post')">content_copy</span></p>
            <p><span class="method">البيانات:</span> <code>{"title": "عنوان المنشور", "content": "محتوى المنشور", "author": "اسم الكاتب", "image_url": "https://picsum.photos/200/300"}</code></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">edit</span> تحديث منشور</h3>
            <p><span class="method">الطريقة:</span> PUT</p>
            <p><span class="method">الرابط:</span> <code class="url" id="update-post"><?php echo $api_base; ?>/posts/1</code> <span class="material-icons copy-btn" onclick="copyToClipboard('update-post')">content_copy</span></p>
            <p><span class="method">البيانات:</span> <code>{"title": "عنوان المنشور المحدث", "content": "محتوى المنشور المحدث", "image_url": "https://picsum.photos/200/300"}</code></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">delete</span> حذف منشور</h3>
            <p><span class="method">الطريقة:</span> DELETE</p>
            <p><span class="method">الرابط:</span> <code class="url" id="delete-post"><?php echo $api_base; ?>/posts/1</code> <span class="material-icons copy-btn" onclick="copyToClipboard('delete-post')">content_copy</span></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">search</span> البحث في المنشورات</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="search-posts"><?php echo $api_base; ?>/posts?search=كلمة البحث</code> <span class="material-icons copy-btn" onclick="copyToClipboard('search-posts')">content_copy</span></p>
        </div>

        <h2 class="mb-4 mt-5">التعليقات</h2>

        <div class="endpoint">
            <h3><span class="material-icons">list</span> الحصول على جميع التعليقات</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="get-all-comments"><?php echo $api_base; ?>/comments</code> <span class="material-icons copy-btn" onclick="copyToClipboard('get-all-comments')">content_copy</span></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">find_in_page</span> الحصول على تعليق واحد</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="get-one-comment"><?php echo $api_base; ?>/comments/1</code> <span class="material-icons copy-btn" onclick="copyToClipboard('get-one-comment')">content_copy</span></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">add_circle</span> إضافة تعليق جديد</h3>
            <p><span class="method">الطريقة:</span> POST</p>
            <p><span class="method">الرابط:</span> <code class="url" id="create-comment"><?php echo $api_base; ?>/comments</code> <span class="material-icons copy-btn" onclick="copyToClipboard('create-comment')">content_copy</span></p>
            <p><span class="method">البيانات:</span> <code>{"post_id": 1, "content": "محتوى التعليق", "author": "اسم الكاتب"}</code></p>
        </div>

        <div class="endpoint">
            <h3><span class="material-icons">list</span> الحصول على تعليقات منشور معين</h3>
            <p><span class="method">الطريقة:</span> GET</p>
            <p><span class="method">الرابط:</span> <code class="url" id="get-post-comments"><?php echo $api_base; ?>/posts/1/comments</code> <span class="material-icons copy-btn" onclick="copyToClipboard('get-post-comments')">content_copy</span></p>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo $base_url; ?>/api.php" class="btn btn-primary btn-lg">
                <span class="material-icons">cloud_download</span> تحميل ملف API
            </a>
            <a href="<?php echo $base_url; ?>/apitest.postman_collection.json" class="btn btn-success btn-lg ms-3">
                <span class="material-icons">description</span> تحميل ملف Postman Collection
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function copyToClipboard(id) {
            const el = document.getElementById(id);
            const text = el.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                toastr.success('تم نسخ الرابط بنجاح: ' + text);
            }).catch(() => {
                toastr.error('فشل نسخ الرابط');
            });
        }

        // تكوين إعدادات toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
</body>
</html>