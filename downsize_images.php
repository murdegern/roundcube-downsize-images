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

    static public function resize_attachment($args) {
        $rcmail = rcube::get_instance();
        $file = $args['path'];
        $size = filesize($file);
        $mime = strtolower($args['mimetype']);
        rcube::write_log('downsize.log', "resize_attachment($args) called; file=($file); size=($size); mime=($mime)");
        if ($size < 1000000 || $mime ~= 'image/jpeg') {
            return $args;
        }
        rcube::write_log('downsize.log', "would downsize this");
        // TODO
        return $args;
    }
}
