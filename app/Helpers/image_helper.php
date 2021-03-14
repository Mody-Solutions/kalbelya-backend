<?php
if (!function_exists('default_image')) {
    function image($image_path = false)
    {
        if (!$image_path || !is_file($image_path)) {
            $image = file_get_contents(public_path(config('kal.profile_img_url')));
            $image = base64_encode($image);
            return "data:image/png;base64,$image";
        } else {
            return false;
        }
    }
}
