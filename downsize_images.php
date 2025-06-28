<?php
/**
 * This downsizes picture uploads if they exceed one MByte.
 */
class downsize_images extends rcube_plugin
{
    public function init()
    {
        $rcmail = rcmail::get_instance();
        $this->add_hook('attachment_upload', [$this, 'resize_attachment']);
    }

    static public function resize_img($img) {
      $size_x = imagesx($img);
      $size_y = imagesy($img);
      $factor = 1.0;
      if ($size_x >= $size_y && $size_x > 1600) {
        $factor = 1600.0 / $size_x;
      } elseif ($size_y >= $size_x && $size_y > 1600) {
        $factor = 1600.0 / $size_y;
      }
      rcube::write_log('downsize.log', "resize_jpeg: size=$size_x x $size_y; factor = $factor");

      $new_img = imagescale($img, (int)(0.5+$size_x*$factor), -1, IMG_BICUBIC_FIXED);

      return $new_img;
    }

    static public function resize_attachment($args) {
        $rcmail = rcube::get_instance();
        $file = $args['path'];
        $size = filesize($file);
        $mime = strtolower($args['mimetype']);
        rcube::write_log('downsize.log', "resize_attachment($args) called; file=($file); size=($size); mime=($mime)");
        if ($size < 1000000 || ($mime != 'image/jpeg' && $mime != 'image/png')) {
            return $args;
        }
        rcube::write_log('downsize.log', "would downsize this");

        if ($mime == 'image/jpeg') {
          $img = imagecreatefromjpeg($filename);
          $img = resize_image($img);
          $success = imagejpeg($img, $filename, -1);
        } elseif ($mime == 'image/png') {
          $img = imagecreatefrompng($filename);
          $img = resize_image($img);
          $success = imagepng($img, $filename, 9);
        }
        rcube::write_log('downsize.log', "re-wrote $filename; success = $success");

        return $args;
    }
}
