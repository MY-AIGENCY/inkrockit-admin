<?php
$glue_types = array('not_glue' => 'Not glue', 'glue_left' => 'Glue left', 'glue_right' => 'Glue right', 'glue_both' => 'Glue both');

if (empty($data['exist_width']) || empty($data['exist_height'])) {
    if (empty($data['width'])) {
        $data['width'] = 0;
    }
    if (empty($data['height'])) {
        $data['height'] = 0;
    }

    if ($data['position'] == 'vertical') {
        $sides1 = $data['width'] * 0.6;
        $sides2 = $data['height'];
    } else {
        $sides1 = $data['width'];
        $sides2 = $data['height'] / 3;
    }
} else {
    $sides1 = $data['exist_width'];
    $sides2 = $data['exist_height'];
}
?>

<label class="lab">Size:</label>
<div class="input_box">
    <input type="text" name="pocket_width[<?= $data['num'] ?>]" value="<?= number_format($sides1, 2, '.', '') ?>" placeholder="width" class="short"> x
    <input type="text" name="pocket_height[<?= $data['num'] ?>]" value="<?= number_format($sides2, 2, '.', '') ?>" placeholder="height" class="short"> 
    <?php
    if ($data['type'] != 'standard') {
        ?> x <input type="text" name="pocket_depth[<?= $data['num'] ?>]" value="<?= ($data['type'] == 'box') ? '0.125' : '0.375'; ?>" placeholder="depth" class="short"><?php
    }
    ?> <small>inch</small>
</div><br class="clear">

<label class="lab">Glue:</label>
<div class="input_box">
    <select name="glue_type[<?= $data['num'] ?>]">
        <?php
        foreach ($glue_types as $k => $v) {
            $sel = '';
            ?><option value="<?= $k ?>" <?php
            if (empty($data['total_count'])) {

                $sel = $data['glue'];
            } else {
                if ($data['total_count'] % 2 == 0) {
                    if ($data['type'] == 'standard' || $data['type'] == 'box') {
                        if ($data['num'] == 1) {
                            $sel = 'glue_left';
                        } else {
                            $sel = 'glue_right';
                        }
                    } else {
                        $sel = 'glue_both';
                    }
                } else {
                    if ($data['type'] == 'standard' || $data['type'] == 'box') {
                        if ($data['num'] == 1) {
                            $sel = 'not_glue';
                        } elseif ($data['num'] == 2) {
                            $sel = 'glue_left';
                        } else {
                            $sel = 'glue_right';
                        }
                    } else {
                        $sel = 'glue_both';
                    }
                }
            }

            if ($k == $sel) {
                echo 'selected="selected"';
            }
            ?>><?= $v ?></option><?php
                }
                ?>
    </select>
</div><br class="clear">