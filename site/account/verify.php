<?php
    if (session_status() == PHP_SESSION_NONE) session_start();

    $width = 200;
    $height = 50;
    $image = imagecreatetruecolor($width, $height);
    imagesetthickness($image, 2);

    $fore_colors_num = rand(4, 15);
    $fore_colors = array();
    for($i = 0; $i < $fore_colors_num; $i++) {
        $fore_colors[] = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    }

    $back_colors_num = rand(25, 128);
    $back_colors = array();
    for($i = 0; $i < $back_colors_num; $i++) {
        $back_colors[] = imagecolorallocatealpha($image, rand(0, 255), rand(0, 255), rand(0, 255), rand(64, 127));
    }

    $rand_action_num = rand(30, 60);
    for($i = 0; $i < $rand_action_num; $i++) {
        $action = rand(0, 6);
        switch($action) {
            case 0: // filled rectangle
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                imagefilledrectangle($image, $x1, $y1, $x2, $y2, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 1: // filled ellipse
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                imagefilledellipse($image, $x1, $y1, $x2, $y2, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 2: // filled polygon
                $points_num = rand(3, 10);
                $points = array();
                for($j = 0; $j < $points_num; $j++) {
                    $points[] = array(rand(0, $width));
                    $points[] = array(rand(0, $height));
                }
                imagefilledpolygon($image, $points, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 3: // filled arc
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                $start = rand(0, 360);
                $end = rand(0, 360);
                $style = rand(0, 3);
                switch($style) {
                    case 0:
                        $style = IMG_ARC_PIE;
                        break;
                    case 1:
                        $style = IMG_ARC_CHORD;
                        break;
                    case 2:
                        $style = IMG_ARC_NOFILL;
                        break;
                    case 3:
                        $style = IMG_ARC_EDGED;
                        break;
                }
                imagefilledarc($image, $x1, $y1, $x2, $y2, $start, $end, $back_colors[rand(0, $back_colors_num - 1)], $style);
                break;
            case 4: // pixel
                $x = rand(0, $width);
                $y = rand(0, $height);
                imagesetpixel($image, $x, $y, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 5: // line
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                imageline($image, $x1, $y1, $x2, $y2, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 6: // arc
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                $start = rand(0, 360);
                $end = rand(0, 360);
                imagearc($image, $x1, $y1, $x2, $y2, $start, $end, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 7: // polygon
                $points_num = rand(3, 10);
                $points = array();
                for($j = 0; $j < $points_num; $j++) {
                    $points[] = array(rand(0, $width));
                    $points[] = array(rand(0, $height));
                }
                imagepolygon($image, $points, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 8: // ellipse
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                imageellipse($image, $x1, $y1, $x2, $y2, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            case 9: // rectangle
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                imagerectangle($image, $x1, $y1, $x2, $y2, $back_colors[rand(0, $back_colors_num - 1)]);
                break;
            default:
                break;
        }

        if($rand_action_num == ($i+10)) {
            imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocatealpha($image, 255, 255, 255, 120));

            $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $chars_num = rand(4, 8);
            $font = '../fonts/TaipeiSansTC-Regular.ttf';
            $auth_code = '';

            for($j = 0; $j < $chars_num; $j++) {
                $size = rand(10*round($height / 4), 10*min(round($width / $chars_num), $height)) * 0.1;
                $char = $chars[rand(0, strlen($chars) - 1)];
                $x = rand(round($j*$width/$chars_num), round(($j+1)*$width/$chars_num-$size));
                $y = rand(round($size), $height - round($size/2));
                $color = $fore_colors[rand(0, $fore_colors_num - 1)];
                $angle = rand(-30, 30);
                imagettftext($image, $size, $angle, $x, $y, $color, $font, $char);
                $auth_code .= $char;

                for($k = 0; $k < round(sqrt($width*$height)); $k++) {
                    imagesetpixel($image, rand(0, $width), rand(0, $height), $color);
                }
            }

            $_SESSION['Verification_Code'] = $auth_code;
        }
    }

    // imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocatealpha($image, 255, 255, 255, 110));

    header('Content-type:image/jpeg');
    imagebmp($image);
    imagedestroy($image);
?>