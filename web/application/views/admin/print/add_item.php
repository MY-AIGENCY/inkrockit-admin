<div id="activity_stats">
    <h3 style="margin-bottom: 10px"><?= (empty($item['title'])) ? 'Add New Product' : 'Edit Product - ' . $item['title']; ?></h3><br>
    <?php
    if (!empty($item)) {
        ?><div style="margin-bottom: 15px;">

            <table class="print_item_details">
                <tr>
                    <td>Title:</td>
                    <td><?= $item['title'] ?></td>
                </tr>
                <tr>
                    <td>ABBR:</td>
                    <td><?= $item['abbr'] ?></td>
                </tr>
                <tr>
                    <td>Product Type:</td>
                    <td><?php
                        if (!empty($categories)) {
                            foreach ($categories as $val) {
                                if ($val['id'] == @$item['category_id']) {
                                    echo $val['title'];
                                }
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Folding Orientation:</td>
                    <td><?php
                        if ($item['folding_orient'] == 'ltr') {
                            echo 'Left to Right';
                        } else {
                            echo 'Top Down';
                        }
                        ?></td>
                </tr>
                <?php
                if (!empty($item_dimentions) && count($item_dimentions) > 1) {
                    foreach ($item_dimentions as $key => $val) {
                        ?>
                        <tr>
                            <td><?= $val['name'] ?>:</td>
                            <td><?= $val['width'] . 'x' . $val['height'] ?></td>                    
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td>Panels count:</td>
                    <td><?= intval($item['panel_count']); ?></td>
                </tr>
                <tr>
                    <td>Slits:</td>
                    <td><?php
                        if (empty($item['slits_type'])) {
                            echo 'No';
                        } else {
                            echo '<span style="text-transform: capitalize">' . $item['slits_type'] . '</span>';
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Paper:</td>
                    <td><?php
                        if (!empty($item['paper_aval'])) {
                            $a_papers = explode(',', $item['paper_aval']);
                            foreach ($a_papers as $v) {
                                echo @$papers[$v]['name'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>INKS, Side1:</td>
                    <td><?php
                        if (!empty($item['inks1_aval'])) {
                            $a_inks = explode(',', $item['inks1_aval']);
                            foreach ($a_inks as $v) {
                                echo @$inks[$v]['name'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>INKS, Side2:</td>
                    <td><?php
                        if (!empty($item['inks2_aval'])) {
                            $a_inks = explode(',', $item['inks2_aval']);
                            foreach ($a_inks as $v) {
                                echo @$inks[$v]['name'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Stitched-in Booklets:</td>
                    <td><?= (!empty($item['stick'])) ? 'Enabled' : 'Disabled'; ?></td>
                </tr>
                <tr>
                    <td>Finishes, Side1:</td>
                    <td><?php
                        if (!empty($item['finishes1_aval'])) {
                            $a_fin = explode(',', $item['finishes1_aval']);
                            foreach ($a_fin as $v) {
                                echo @$finishes[$v]['title'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Finishes, Side2:</td>
                    <td><?php
                        if (!empty($item['finishes2_aval'])) {
                            $a_fin = explode(',', $item['finishes2_aval']);
                            foreach ($a_fin as $v) {
                                echo @$finishes[$v]['title'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Coating, Side1:</td>
                    <td><?php
                        if (!empty($item['coating1_aval'])) {
                            $a_coat = explode(',', $item['coating1_aval']);
                            foreach ($a_coat as $v) {
                                echo @$coating[$v]['title'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Coating, Side2:</td>
                    <td><?php
                        if (!empty($item['coating2_aval'])) {
                            $a_coat = explode(',', $item['coating2_aval']);
                            foreach ($a_coat as $v) {
                                echo @$coating[$v]['title'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Proof:</td>
                    <td><?php
                        if (!empty($item['proof_aval'])) {
                            $a_proof = explode(',', $item['proof_aval']);
                            foreach ($a_proof as $v) {
                                echo @$proof[$v]['name'] . '; ';
                            }
                        }
                        ?></td>
                </tr>
            </table>
        </div><?php
    }
    ?>
</div>

<div class="msgbar msg_Error hide"><p></p></div>

<?php if (@$_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'] && !empty($_POST['title'])) { ?>
    <div class="msgbar msg_Success hide_onC">
        <span class="iconsweet">=</span><p>Information Saved</p>
    </div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">General</a></li>
            <li><a href="#tabs-11">Panels</a></li>
            <li><a href="#tabs-3">Paper</a></li>
            <li><a href="#tabs-4">INKS</a></li>
            <li <?php if (empty($item['category_id']) || $item['category_id'] != 1) echo 'style="display:none"'; ?>>
                <a href="#tabs-7">Stitched-in Booklets</a>
            </li>
            <li><a href="#tabs-5">Finishes</a></li>
            <li><a href="#tabs-6">Coating</a></li>
            <li><a href="#tabs-9">Proof</a></li>
            <li><a href="#tabs-8">Template/Images</a></li>
            <li><a href="#tabs-10">Price</a></li>
        </ul>
        <div id="tabs-1">
            <ul class="form_fields_container">
                <li>
                    <label>Product Type:</label>
                    <div class="form_input">
                        <select name="category" data-type="print">
                            <option value=""></option>
                            <?php
                            if (!empty($categories)) {
                                foreach ($categories as $val) {
                                    ?><option <?php if ($val['id'] == @$item['category_id']) echo'selected="selected"' ?> value="<?= $val['id'] ?>"><?= $val['title'] ?></option><?php
                                }
                            }
                            ?>
                        </select>
                        <span class="ui-icon ui-icon-plus pointer" id="add_category" style="margin: 5px"></span>
                    </div>
                    <br class="clear">

                    <label>Width:</label>
                    <div class="form_input">
                        <input type="text" name="width" <?= (!empty($item['width'])) ? 'value="' . htmlspecialchars($item['width']) . '"' : '' ?> class="small"> <small>inch</small>
                    </div>
                    <br class="clear">

                    <label>Height:</label>
                    <div class="form_input">
                        <input type="text" name="height" <?= (!empty($item['height'])) ? 'value="' . htmlspecialchars($item['height']) . '"' : '' ?> class="small"> <small>inch</small>
                    </div>
                    <br class="clear">


                    <label>Folding Orientation:</label>
                    <div class="form_input">
                        <select name="fold_orient">
                            <option value="ltr" <?php if (!empty($item['folding_orient']) && $item['folding_orient'] == 'ltr') echo 'selected="selected"'; ?>>Left to Right</option>
                            <option value="ttd" <?php if (!empty($item['folding_orient']) && $item['folding_orient'] == 'ttd') echo 'selected="selected"'; ?>>Top Down</option>
                        </select>
                    </div>
                    <br class="clear">
                    <label>Folding Types:</label>
                    <div class="form_input">
                        <table>
                            <?php
                            if (!empty($foldings)) {
                                $folding_type = (empty($item) || empty($item['folding_types'])) ? array() : explode(',', $item['folding_types']);
                                foreach ($foldings as $key => $val) {
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="folding_type[]" value="<?= $key ?>" <?php if (!empty($folding_type) && in_array($key, $folding_type)) echo 'checked="checked"'; ?>> <?= $val['title'] ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>
                    <br class="clear">
                    <br>
                    <label>Active?</label>
                    <div class="form_input">
                        <select name="active">
                            <option value="1">Yes</option>
                            <option value="0" <?= (!empty($item) && $item['active'] == 0) ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>

                </li>
                <?php /*
                  <li>
                  <label>Name:</label>
                  <div class="form_input">
                  <input type="text" readonly="readonly" disabled="disabled" name="title" <?= (!empty($item['title'])) ? 'value="' . htmlspecialchars($item['title']) . '"' : '' ?>>
                  <br><small>*will be generated automatically</small>
                  </div>
                  <br class="clear">

                  <label>ABBR:</label>
                  <div class="form_input">
                  <input type="text" readonly="readonly" disabled="disabled" name="abbr" <?= (!empty($item['abbr'])) ? 'value="' . htmlspecialchars($item['abbr']) . '"' : '' ?>>
                  <br><small>*will be generated automatically</small>
                  </div>
                  <br class="clear">
                  </li>
                 */ ?>
            </ul>
        </div>
        <?php /*
          <div id="tabs-2">
          <ul class="form_fields_container">
          <li>
          <div id="dimmention_form">

          <?php
          if (!empty($item_dimentions) && count($item_dimentions) > 1) {
          foreach ($item_dimentions as $key => $val) {
          ?>
          <div class="dim_form">
          <label><!--<span class="ui-icon ui-icon-minus left pointer remove_dim"></span>-->
          Name:</label>
          <div class="form_input">
          <input type="text" name="dim_name[]" class="left" value="<?= $val['name']; ?>">
          </div>
          <br class="clear">
          <label>Width:</label>
          <div class="form_input">
          <input type="text" name="dim_width[]" class="left small" value="<?= $val['width']; ?>"> <small>inch</small>
          </div>
          <br class="clear">
          <label>Height:</label>
          <div class="form_input">
          <input type="text" name="dim_height[]" class="left small" value="<?= $val['height']; ?>"> <small>inch</small>
          </div>
          <br class="clear">
          <label>ABBR:</label>
          <div class="form_input">
          <input type="text" name="dim_abbr[]" class="left small" value="<?= $val['abbr']; ?>">
          </div>
          <br class="clear">
          <label class="left">Description:</label>
          <div class="form_input">
          <textarea name="dim_descr[]" class="left"><?= $val['description']; ?></textarea>
          </div>
          <br class="clear">
          <hr>
          </div>
          <?php
          }
          } else {
          ?>
          <div class="dim_form">
          <label>Name:</label>
          <div class="form_input">
          <input type="text" name="dim_name[]" class="left" value="Open/Flat size (w/ bleed)">
          </div>
          <br class="clear">
          <label>Width:</label>
          <div class="form_input">
          <input type="text" name="dim_width[]" class="left small" value=""> <small>inch</small>
          </div>
          <br class="clear">
          <label>Height:</label>
          <div class="form_input">
          <input type="text" name="dim_height[]" class="left small" value=""> <small>inch</small>
          </div>
          <br class="clear">
          <label>ABBR:</label>
          <div class="form_input">
          <input type="text" name="dim_abbr[]" class="left small" value="">
          </div>
          <br class="clear">
          <label class="left">Description:</label>
          <div class="form_input">
          <textarea name="dim_descr[]"  class="left">Size of input paper</textarea>
          </div>
          <br class="clear">
          </div>
          <div class="dim_form">
          <hr>
          <label>
          <!--<span class="ui-icon ui-icon-minus left pointer remove_dim"></span>-->
          Name:</label>
          <div class="form_input">
          <input type="text" name="dim_name[]" class="left" value="Finished size">
          </div>
          <br class="clear">
          <label>Width:</label>
          <div class="form_input">
          <input type="text" name="dim_width[]" class="left small" value=""> <small>inch</small>
          </div>
          <br class="clear">
          <label>Height:</label>
          <div class="form_input">
          <input type="text" name="dim_height[]" class="left small" value=""> <small>inch</small>
          </div>
          <br class="clear">
          <label>ABBR:</label>
          <div class="form_input">
          <input type="text" name="dim_abbr[]" class="left small" value="">
          </div>
          <br class="clear">
          <label class="left">Description:</label>
          <div class="form_input">
          <textarea name="dim_descr[]" class="left">Size of output product</textarea>
          </div>
          <br class="clear">
          </div>
          <?php
          }
          ?>
          </div>
          </li>
          </ul>
          </div>

         */ ?>
        <div id="tabs-3">
            <ul class="form_fields_container">
                <li>
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_paper_field">
                        <span class="iconsweet">+</span> Add
                    </a>
                    <table class="activity_datatable small_datatable paper_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                        <tr>
                            <th width="104"><input type="checkbox" name="check_all"> Available</th>
                            <th>Name</th>
                            <th width="80">Default</th>
                        </tr>
                        <?php
                        if (!empty($papers)) {
                            foreach ($papers as $val) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="use_paper[]" <?php
                                        $used_items = (!empty($item['paper_aval'])) ? explode(',', $item['paper_aval']) : array();
                                        if (in_array($val['id'], $used_items))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>" class="short">
                                        <span class="ui-icon ui-icon-pencil pointer right edit_paper" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['description'] ?>"></span>
                                    </td>
                                    <td><?= $val['name'] ?></td>
                                    <td><input type="radio" name="paper_default" <?php if (@$item['paper_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>"></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr class="hide">
                            <td colspan="2">
                                <label>Name:</label>
                                <div class="form_input">
                                    <input type="text" class="left" name="paper_name">
                                </div>
                                <br class="clear">
                                <label>Description:</label>
                                <div class="form_input">
                                    <input type="text" name="paper_description">
                                </div>
                                <br class="clear">
                                <label>Price, $:</label>
                                <div class="form_input">
                                    <input type="text" name="paper_price" class="small">
                                    for count:
                                    <input type="text" name="paper_count" class="small">
                                    <a class="button_small whitishBtn" id="add_paper">Add</a>
                                </div>
                                <br class="clear">

                            </td>
                        </tr>
                    </table>  
                </li>
            </ul>
        </div>
        <div id="tabs-4">
            <ul class="form_fields_container">
                <li>
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_inks_field">
                        <span class="iconsweet">+</span> Add
                    </a>
                    <table class="activity_datatable small_datatable inks_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                        <tr>
                            <th width="84">
                                Side 1<br>
                                Aval/Def
                            </th>
                            <th width="84">
                                Side 2<br>
                                Aval/Def
                            </th>
                            <th width="70"><br>Action</th>
                            <th><br>Name</th>
                            <th width="100"><br>Price, $</th>
                        </tr>
                        <?php
                        if (!empty($inks)) {
                            foreach ($inks as $val) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" style="margin-left: 15px" name="inks1_use[]" <?php
                                        $inks1_aval = (!empty($item['inks1_aval'])) ? explode(',', $item['inks1_aval']) : array();
                                        if (in_array($val['id'], $inks1_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="inks1_default" <?php if (@$item['inks1_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" style="margin-left: 15px" name="inks2_use[]" <?php
                                        $inks2_aval = (!empty($item['inks2_aval'])) ? explode(',', $item['inks2_aval']) : array();
                                        if (in_array($val['id'], $inks2_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="inks2_default" <?php if (@$item['inks2_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td>
                                        <span class="ui-icon ui-icon-pencil pointer right edit_ink" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['description'] ?>"></span>
                                    </td>
                                    <td class="editable"><?= $val['name'] ?></td>
                                    <td><?= $val['price'] ?></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr <?php if (!empty($inks)) echo'class="hide"' ?>>
                            <td></td>
                            <td colspan="3"><label>Name:</label>
                                <div class="form_input">
                                    <input type="text" class="left" name="inks_name">
                                </div>
                                <br class="clear">
                                <label>Price:</label>
                                <div class="form_input">
                                    <input type="text" name="inks_price">
                                </div>
                                <br class="clear">
                                <label>Description:</label>
                                <div class="form_input">
                                    <input type="text" name="inks_description">
                                </div>
                                <br class="clear">
                            </td>
                            <td><a class="button_small whitishBtn" id="add_inks">Add</a></td>
                        </tr>
                    </table>  
                </li>
            </ul>
        </div>
        <div id="tabs-5">
            <ul class="form_fields_container">
                <li>
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_finish_field">
                        <span class="iconsweet">+</span> Add
                    </a>

                    <table class="activity_datatable small_datatable finish_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                        <tr>
                            <th width="84">
                                Side 1<br>
                                Aval/Def
                            </th>
                            <th width="84">
                                Side 2<br>
                                Aval/Def
                            </th>
                            <th width="70"><br>Action</th>
                            <th><br>Name</th>
                        </tr>
                        <?php
                        if (!empty($finishes)) {
                            foreach ($finishes as $val) {
                                ?>
                                <tr>
                                    <td width="50">
                                        <input type="checkbox" style="margin-left: 15px" name="finishes1_aval[]" <?php
                                        $finish1_aval = (!empty($item['finishes1_aval'])) ? explode(',', $item['finishes1_aval']) : array();
                                        if (in_array($val['id'], $finish1_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="finish1_def" <?php if (@$item['finishes1_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td width="50">
                                        <input type="checkbox" style="margin-left: 15px" name="finishes2_aval[]" <?php
                                        $finish2_aval = (!empty($item['finishes2_aval'])) ? explode(',', $item['finishes2_aval']) : array();
                                        if (in_array($val['id'], $finish2_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="finish2_def" <?php if (@$item['finishes2_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td>
                                        <span class="ui-icon ui-icon-pencil pointer right edit_finish" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['abbr'] ?>"></span>
                                    </td>
                                    <td class="editable"><?= $val['title'] ?></td>
                                </tr><?php
                            }
                        }
                        ?>
                        <tr class="hide">
                            <td colspan="4">
                                <label>Name:</label>
                                <div class="form_input">
                                    <input type="text" class="left" name="finish_name">
                                </div>
                                <br class="clear">
                                <label>ABBR:</label>
                                <div class="form_input">
                                    <input type="text" name="finish_abbr">
                                </div>
                                <br class="clear">
                                <label>Days:</label>
                                <div class="form_input">
                                    <input type="text" name="finish_days">
                                </div>
                                <br class="clear">
                                <label>Price, $:</label>
                                <div class="form_input">
                                    <input type="text" name="finish_price" class="small"> for count: 
                                    <input type="text" name="finish_count" class="small"> <a class="button_small whitishBtn" id="add_finish">Add</a>
                                </div>
                                <br class="clear">
                            </td>
                        </tr>
                    </table>
                </li>
            </ul>
        </div>
        <div id="tabs-6">
            <ul class="form_fields_container">
                <li>
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_coat_field">
                        <span class="iconsweet">+</span> Add
                    </a>                    

                    <table class="activity_datatable small_datatable coat_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                        <tr>
                            <th width="84">
                                Side 1<br>
                                Aval/Def
                            </th>
                            <th width="84">
                                Side 2<br>
                                Aval/Def
                            </th>
                            <th width="70"><br>Action</th>
                            <th><br>Name</th>
                        </tr>
                        <?php
                        if (!empty($coating)) {
                            foreach ($coating as $val) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" style="margin-left: 15px" name="coating1_aval[]" <?php
                                        $coat1_aval = (!empty($item['coating1_aval'])) ? explode(',', $item['coating1_aval']) : array();
                                        if (in_array($val['id'], $coat1_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="coating1_def" <?php if (@$item['coating1_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td><input type="checkbox" style="margin-left: 15px" name="coating2_aval[]" <?php
                                        $coat2_aval = (!empty($item['coating2_aval'])) ? explode(',', $item['coating2_aval']) : array();
                                        if (in_array($val['id'], $coat2_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <input type="radio" name="coating2_def" <?php if (@$item['coating2_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                    <td>
                                        <span class="ui-icon ui-icon-pencil pointer right edit_coating" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['abbr'] ?>" data-hasqtip="true"></span>
                                    </td>
                                    <td class="editable"><?= $val['title'] ?></td>
                                </tr><?php
                            }
                        }
                        ?>
                        <tr class="hide">
                            <td colspan="4">
                                <label>Name:</label>
                                <div class="form_input">
                                    <input type="text" class="left" name="coat_name">
                                </div>
                                <br class="clear">
                                <label>ABBR:</label>
                                <div class="form_input">
                                    <input type="text" name="coat_abbr">
                                </div>
                                <br class="clear">
                                <label>Days:</label>
                                <div class="form_input">
                                    <input type="text" name="coat_days">
                                </div>
                                <br class="clear">
                                <label>Price, $:</label>
                                <div class="form_input">
                                    <input type="text" name="coat_price" class="small"> for count: 
                                    <input type="text" name="coat_count" class="small"> <a class="button_small whitishBtn" id="add_coat">Add</a>
                                </div>
                                <br class="clear">
                            </td>
                        </tr>
                    </table>
                </li>
            </ul>
        </div>
        <div id="tabs-7">
            <ul class="form_fields_container">
                <li>
                    <div class="input_box">
                        <input type="checkbox" name="sticked" class="marg_l10 left" <?php if (!empty($item['stick'])) echo'checked="checked"' ?>>
                        <h6 class="left">Enable</h6>
                    </div>

                    <div class="form_input" style="width: 85%; <?php if (empty($item['stick'])) echo'display:none' ?>" id="stick_block">
                        <br>
                        <h6 style="font-size: 15px">Flat Size:</h6><br class="clear">
                        <label class="left">Width:</label><input name="stick_flat_width" type="text" value="<?= @$sticked['flat_width'] ?>" class="small left"> <small>inch</small><br class="clear">
                        <label class="left">Height:</label><input name="stick_flat_height" type="text" value="<?= @$sticked['flat_height'] ?>" class="small left"> <small>inch</small><br class="clear">
                        <label class="left">Description:</label><input name="stick_flat_description" value="<?= @$sticked['flat_description'] ?>" type="text" class="left">
                        <br class="clear">
                        <hr>

                        <h6 style="font-size: 15px">Finish Size:</h6><br class="clear">
                        <label class="left">Width:</label><input name="stick_finish_width" type="text" value="<?= @$sticked['finish_width'] ?>" class="small left"> <small>inch</small><br class="clear">
                        <label class="left">Height:</label><input name="stick_finish_height" type="text" value="<?= @$sticked['finish_height'] ?>" class="small left"> <small>inch</small><br class="clear">
                        <label class="left">Description:</label><input name="stick_finish_description" value="<?= @$sticked['finish_description'] ?>" type="text" class="left">
                        <br class="clear">

                        <hr>
                        <br>
                        <h5>Paper</h5><br>
                        <div class="input_box">
                            <?php
                            if (!empty($papers)) {
                                $chunk_count = ceil(count($papers) / 2);
                                $papers_chunk = array_chunk($papers, $chunk_count);
                                for ($x = 0; $x < 2; $x++) {
                                    ?>
                                    <table class="activity_datatable small_datatable left" width="49%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF; margin-left: 5px;">
                                        <tr>
                                            <th width="110">Aval, Default</th>
                                            <th>Name</th>
                                        </tr>
                                        <?php
                                        if (!empty($papers_chunk[$x])) {
                                            foreach ($papers_chunk[$x] as $val) {
                                                ?>
                                                <tr>
                                                    <td><input type="checkbox" name="stick_use_paper[]" <?php
                                                        $used_items = (!empty($sticked['paper_aval'])) ? explode(',', $sticked['paper_aval']) : array();
                                                        if (in_array($val['id'], $used_items))
                                                            echo 'checked="checked"';
                                                        ?> value="<?= $val['id'] ?>" class="short">
                                                        <span class="ui-icon ui-icon-pencil pointer right edit_paper" data-id="<?= $val['id'] ?>"></span>
                                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['description'] ?>"></span>
                                                        <input type="radio" name="stick_paper_default" <?php if (@$sticked['paper_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                                    </td>
                                                    <td><?= $val['name'] ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <br class="clear">

                        <br><h5>INKs</h5><br>
                        <div class="input_box">
                            <?php
                            if (!empty($inks)) {
                                $chunk_count = ceil(count($inks) / 2);
                                $inks_chunk = array_chunk($inks, $chunk_count);
                                for ($x = 0; $x < 2; $x++) {
                                    ?>
                                    <table class="activity_datatable small_datatable left" width="49%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF; margin-left: 5px;">
                                        <tr>
                                            <th width="100">Aval, Default</th>
                                            <th>Name</th>
                                            <th width="50">Price, $</th>
                                        </tr>
                                        <?php
                                        if (!empty($inks_chunk[$x])) {
                                            foreach ($inks_chunk[$x] as $val) {
                                                ?>
                                                <tr>
                                                    <td><input type="checkbox" name="stick_inks_use[]" <?php
                                                        $inks_aval = (!empty($sticked['inks_aval'])) ? explode(',', $sticked['inks_aval']) : array();
                                                        if (in_array($val['id'], $inks_aval))
                                                            echo 'checked="checked"';
                                                        ?> value="<?= $val['id'] ?>" class="short left">
                                                        <input type="radio" class="left" name="stick_inks_default" <?php if (@$sticked['inks_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                                        <span class="ui-icon ui-icon-pencil pointer right edit_ink" data-id="<?= $val['id'] ?>"></span>
                                                        <span class="ui-icon ui-icon-help pointer left qtips" title="<?= $val['description'] ?>"></span>
                                                    </td>
                                                    <td class="editable"><?= $val['name'] ?></td>
                                                    <td><?= $val['price'] ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <br class="clear">


                        <br><h5>Finishes</h5><br>
                        <div class="input_box">
                            <?php
                            if (!empty($finishes)) {
                                $chunk_count = ceil(count($finishes) / 2);
                                $finish_chunk = array_chunk($finishes, $chunk_count);
                                for ($x = 0; $x < 2; $x++) {
                                    ?>
                                    <table class="activity_datatable small_datatable left" width="49%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF; margin-left: 5px;">
                                        <tr>
                                            <th width="50">Aval, Def</th>
                                            <th>Name</th>
                                        </tr>
                                        <?php
                                        if (!empty($finish_chunk[$x])) {
                                            foreach ($finish_chunk[$x] as $val) {
                                                ?>
                                                <tr>
                                                    <td width="70"><input type="checkbox" name="stick_finishes_aval[]" <?php
                                                        $inks_aval = (!empty($sticked['finishes_aval'])) ? explode(',', $sticked['finishes_aval']) : array();
                                                        if (in_array($val['id'], $inks_aval))
                                                            echo 'checked="checked"';
                                                        ?> value="<?= $val['id'] ?>">
                                                        <span class="ui-icon ui-icon-pencil pointer right edit_finish" data-id="<?= $val['id'] ?>"></span>
                                                        <input type="radio" name="stick_finish_def" <?php if (@$sticked['finishes_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                                    </td>
                                                    <td class="editable"><?= $val['title'] ?></td>
                                                </tr><?php
                                            }
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                            }
                            ?>
                        </div><br class="clear">
                    </div>
                </li>
            </ul>
        </div>
        <div id="tabs-9">
            <ul class="form_fields_container">
                <li>
                    <?php
                    if (!empty($proof)) {
                        ?>
                        <table class="activity_datatable small_datatable proof_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                            <tr>
                                <th width="104"><input type="checkbox" name="check_all"> Available</th>
                                <th>Name</th>
                                <th width="100">Price, $</th>
                                <th width="80">Default</th>
                            </tr> 
                            <?php
                            foreach ($proof as $val) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="proof_aval[]" <?php
                                        $slits_aval = (!empty($item['proof_aval'])) ? explode(',', $item['proof_aval']) : array();
                                        if (in_array($val['id'], $slits_aval))
                                            echo 'checked="checked"';
                                        ?> value="<?= $val['id'] ?>">
                                        <span class="ui-icon ui-icon-pencil pointer right edit_proof" data-id="<?= $val['id'] ?>"></span>
                                    </td>
                                    <td><?= $val['name'] ?></td>
                                    <td><?= $val['price'] ?></td>
                                    <td>
                                        <input type="radio" name="proof_default" <?php if (@$item['proof_def'] == $val['id']) echo'checked="checked"' ?> value="<?= $val['id'] ?>">
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table><?php
                    }
                    ?>
                </li>
            </ul>
        </div>
        <div id="tabs-10">
            <ul class="form_fields_container">
                <li>
                    <a style="margin:5px;" class="button_small whitishBtn right add_coat_price"><span class="iconsweet">+</span>Add Price</a>
                    <div class="form_input" style="float: left">
                        <table class="activity_datatable prices_db" width="60%" border="0" cellspacing="0" cellpadding="3" style="background: #f8f8f8">
                            <tbody>
                                <tr>
                                    <th width="60">Count</th>
                                    <th>Price for ONE, $</th>
                                    <th width="60">Action</th>
                                </tr>
                                <?php
                                if (!empty($item_prices)) {
                                    foreach ($item_prices as $val) {
                                        ?><tr>
                                            <td>
                                                <input type="text" value="<?= $val['count'] ?>" name="count[]" class="short">
                                            </td>
                                            <td>
                                                <input type="text" name="price[]" value="<?= $val['price'] ?>" class="short">
                                            </td>
                                            <td class="iconsweet"><a class="tip_north remove_coat">X</a></td>
                                        </tr><?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <br class="clear">
                    <br>

                    <label>Minimum Price: $</label>
                    <div class="form_input">
                        <input type="text" name="min_price" value="<?= @$item['min_price'] ?>" class="short">
                    </div>
                    <br class="clear">
                    <label>Minimum Count:</label>
                    <div class="form_input">
                        <input type="text" name="min_count" value="<?= @$item['min_count'] ?>" class="short">
                    </div>
                </li>
            </ul>
        </div>
        <div id="tabs-11">
            <ul class="form_fields_container">
                <li>
                    <label>Panels Count:</label>
                    <div class="form_input">
                        <input type="number" min="0" name="panels_count" value="<?php
                        if (!empty($item['panel_count'])) {
                            echo $item['panel_count'];
                        } else {
                            echo '2';
                        }
                        ?>" class="short">
                    </div>
                    <br class="clear">
                    <label>Panels Width:</label>
                    <div class="form_input">
                        <input type="text" name="panels_width" value="<?= @$item['panel_width'] ?>" class="short"> <small>inch</small>
                    </div>
                    <br class="clear">
                    <label>Panels Height:</label>
                    <div class="form_input">
                        <input type="text" name="panels_height" value="<?= @$item['panel_height'] ?>" class="short"> <small>inch</small>
                    </div>
                    <br class="clear">
                </li>
                <li>
                    <input type="checkbox" name="have_pockets" value="1" <?php
                    if (!empty($packs)) {
                        echo 'checked="checked"';
                    }
                    ?>> Does it have a pocket?<br><br>
                    <div class="pockets_block">
                        <?php
                        if (!empty($item['panel_count']) && !empty($packs)) {
                            for ($x = 1; $x <= $item['panel_count']; $x++) {
                                ?>
                                <div class="one_block" data-num="<?= $x ?>">
                                    <h4 style="font-size: 18px">Panel #<?= $x  ?></h4>
                                    <label class="lab">Count:</label>
                                    <input type="number" name="pocket_count[<?= $x  ?>]" value="<?= (empty($packs[$x]['counts'])) ? 0 : $packs[$x]['counts']; ?>" min="0">
                                    <br class="clear">
                                    <div class="pocket_options" <?php if (empty($packs[$x]['counts'])) echo'style="display: none"'?>>
                                        <label class="lab">Position:</label>
                                        <select name="pocket_position[<?= $x  ?>]">
                                            <option value="horizontal">Horizontal</option>
                                            <option value="vertical" <?php if (@$packs[$x]['position'] == 'vertical') echo'selected="selected"' ?>>Vertical</option>
                                        </select>
                                        <br class="clear">
                                        <label class="lab">Type:</label>
                                        <select name="pack_type[<?= $x  ?>]">
                                            <option value="">-</option>
                                            <option value="standard" <?php if (@$packs[$x]['type'] == 'standard') echo'selected="selected"' ?>>Standard</option>
                                            <option value="expandable" <?php if (@$packs[$x]['type'] == 'expandable') echo'selected="selected"' ?>>Expandable</option>
                                            <option value="box" <?php if (@$packs[$x]['type'] == 'box') echo'selected="selected"' ?>>Box</option>
                                        </select><br class="clear">
                                        <div class="pack_type">
                                            <?php
                                            if (!empty($packs[$x]['counts'])) {
                                                $size = (empty($packs[$x]['size'])) ? array() : unserialize($packs[$x]['size']);
                                                echo View::factory('block_admin/print_packets_table', array('data' => array(
                                                        'exist_width' => @$size['width'],
                                                        'exist_height' => @$size['height'],
                                                        'exist_depth' => @$size['depth'],
                                                        'position' => @$packs[$x]['position'],
                                                        'num' => $x,
                                                        'glue' => @$packs[$x]['glue'],
                                                        'type' => @$packs[$x]['type']
                                                )));
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>    
                                <?php
                            }
                        }
                        ?>
                    </div>
                </li>
                <li>

                    <input type="checkbox" name="have_slits" value="1" <?php if (!empty($item['slits_type'])) echo 'checked="checked"' ?>>
                    Does it have a Slits?
                    <br>

                    <ul class="form_fields_container <?php if (empty($item['slits_type'])) echo 'hide' ?>" id="slits_container">
                        <li>
                            <label style="width: 100px">Type: </label>
                            <div class="input_box">
                                <select name="slit_type">
                                    <option value="">-</option>
                                    <option value="multiple" <?php if (!empty($item['slits_type']) && $item['slits_type'] == 'multiple') echo 'selected="selected"' ?>>Multiple</option>
                                    <option value="single" <?php if (!empty($item['slits_type']) && $item['slits_type'] == 'single') echo 'selected="selected"' ?>>Single</option>
                                </select>
                            </div>
                        </li>
                        <li class="slit_type_block">
                            <?php
                            if (!empty($item['slits_type'])) {
                                if ($item['slits_type'] == 'single') {
                                    //single 
                                    ?>
                                    <label style="width: 100px">Position:</label>
                                    <select name="single_position">
                                        <option value="">-</option>
                                        <?php
                                        //panels
                                        if (!empty($item['panel_count'])) {
                                            for ($x = 0; $x < $item['panel_count']; $x++) {
                                                ?><option value="panel-<?= $x + 1 ?>" <?php if (!empty($item_slits) && !empty($item_slits[0]) && $item_slits[0]['type'] == 'panel' && $item_slits[0]['page_num'] == $x + 1) echo 'selected="selected"'; ?>>Panel #<?= $x + 1 ?></option><?php
                                            }
                                        }
                                        //pockets
                                        if (!empty($packs)) {
                                            foreach ($packs as $k => $v) {
                                                if (!empty($v['type'])) {
                                                    ?><option value="panel-<?= $k ?>" <?php if (!empty($item_slits) && !empty($item_slits[0]) && $item_slits[0]['type'] == 'pocket' && $item_slits[0]['page_num'] == $k) echo 'selected="selected"'; ?>>Pocket #<?= $k  ?></option><?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <br>
                                    <div class="slits_table <?php if (empty($item_slits) || empty($item_slits[0])) echo 'hide' ?>">
                                        <?php
                                        $aval = (empty($item_slits) || empty($item_slits[0]) || empty($item_slits[0]['slit_aval'])) ? array() : unserialize($item_slits[0]['slit_aval']);
                                        $def = (empty($item_slits) || empty($item_slits[0]) || empty($item_slits[0]['slit_def'])) ? '' : $item_slits[0]['slit_def'];
                                        echo View::factory('block_admin/print_slits_table', array('slits' => $slits, 'num' => 1, 'aval' => $aval, 'def' => $def, 'type' => 'any'));
                                        ?>
                                    </div>
                                    <?php
                                } else {

                                    //multiple panels

                                    if (!empty($item['panel_count'])) {
                                        for ($x = 0; $x < $item['panel_count']; $x++) {
                                            $aval = $def = NULL;
                                            foreach ($item_slits as $it) {
                                                if ($it['type'] == 'panel' && $it['page_num'] == $x + 1 && empty($aval)) {
                                                    $aval = unserialize($it['slit_aval']);
                                                }
                                                if (empty($def) && $it['type'] == 'panel' && $it['page_num'] == $x + 1) {
                                                    $def = $it['slit_def'];
                                                }
                                            }
                                            ?>
                                            <div class="col2">
                                                <h5><input class="multiple_slit" type="checkbox" name="multiple[panel][<?= $x + 1 ?>]" <?php if (!empty($aval)) echo'checked="checked"'; ?>> Panel #<?= $x + 1 ?></h5>
                                                <div class="slits_table <?php if (empty($aval)) echo 'hide'; ?>">
                                                    <?php
                                                    echo View::factory('block_admin/print_slits_table', array('slits' => $slits, 'num' => $x + 1, 'aval' => $aval, 'def' => $def, 'type' => 'panel'));
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    echo '<br class="clear"><hr><br class="clear">';
                                    //pockets
                                    if (!empty($packs)) {
                                        foreach ($packs as $k => $v) {
                                            if (!empty($v['type'])) {
                                                $aval = $def = NULL;
                                                foreach ($item_slits as $it) {
                                                    if ($it['type'] == 'pocket' && $it['page_num'] == $k  && empty($aval)) {
                                                        $aval = unserialize($it['slit_aval']);
                                                    }
                                                    if (empty($def) && $it['type'] == 'pocket' && $it['page_num'] == $k) {
                                                        $def = $it['slit_def'];
                                                    }
                                                }
                                                if (empty($aval)) {
                                                    $aval = '';
                                                }
                                                if (empty($def)) {
                                                    $def = '';
                                                }
                                                ?>
                                                <div class="col2">
                                                    <h5><input class="multiple_slit" type="checkbox" name="multiple[pocket][<?= $k  ?>]" <?php if (!empty($aval)) echo'checked="checked"'; ?>> Pocket #<?= $k ?></h5>
                                                    <div class="slits_table <?php if (empty($aval)) echo 'hide'; ?>">
                                                        <?php
                                                        echo View::factory('block_admin/print_slits_table', array('slits' => $slits, 'num' => $k , 'aval' => $aval, 'def' => $def, 'type' => 'pocket'));
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>                    
                        </li>
                    </ul>

                </li>
            </ul>
        </div>
        <div id="tabs-8">

            <ul class="form_fields_container">
                <li>
                    <label>Template PSD:</label>
                    <div class="form_input">
                        <?php
                        if (is_file(APPPATH . 'files/print/psd/' . @$item['id'] . '.psd')) {
                            ?>
                            <div>
                                <img src="/images/DownloadPs.png" class="left">
                                <a class="ui-icon ui-icon-closethick left" id="remove_psd" data-id="<?= $item['id'] ?>">del</a>
                                <br class="clear">
                            </div>
                            <?php
                        }
                        ?>
                        <input type="file" name="template_psd">
                    </div>
                    <br class="clear">

                    <label>Template Small Preview:</label>
                    <div class="form_input">
                        <?php
                        if (is_file(APPPATH . 'files/print/preview/' . @$item['id'] . '.jpg')) {
                            ?>
                            <div>
                                <img src="/files/print/preview/<?= $item['id'] ?>.jpg?v=<?= rand(1, 999) ?>" class="left">
                                <a class="ui-icon ui-icon-closethick left" id="remove_preview" data-id="<?= $item['id'] ?>">del</a>
                                <br class="clear">
                            </div><?php
                        }
                        ?>
                        <input type="file" name="template_preview">
                    </div>
                    <br class="clear">

                    <label>Template Small Active<br> Preview:</label>
                    <div class="form_input">
                        <?php
                        if (is_file(APPPATH . 'files/print/active_preview/' . @$item['id'] . '.jpg')) {
                            ?>
                            <div>
                                <img src="/files/print/active_preview/<?= $item['id'] ?>.jpg?v=<?= rand(1, 999) ?>" class="left">
                                <a class="ui-icon ui-icon-closethick left" id="remove_act_preview" data-id="<?= $item['id'] ?>">del</a>
                                <br class="clear">
                            </div><?php
                        }
                        ?>
                        <input type="file" name="template_active_preview">
                    </div>

                    <br class="clear">
                    <label>Product View:</label>
                    <div class="form_input">
                        <?php
                        if (is_file(APPPATH . 'files/print/view/' . @$item['id'] . '.jpg')) {
                            ?>
                            <div>
                                <img src="/files/print/view/<?= $item['id'] ?>.jpg?v=<?= rand(1, 999) ?>" class="left" width="150">
                                <a class="ui-icon ui-icon-closethick left" id="remove_view" data-id="<?= $item['id'] ?>">del</a>
                                <br class="clear">
                            </div><?php
                        }
                        ?>
                        <input type="file" name="template_view">
                    </div>
                    <br class="clear">
                </li>
            </ul>
        </div>

    </div>

    <ul class="form_fields_container submit_block">
        <li>
            <?php if (!empty($item)) { ?>
                <input type="submit" class="button_small whitishBtn print_edit_page" name="save_prev" value="< Save and Prev" style="margin-left: 20px">
            <?php } ?>
            <input type="submit" class="button_small whitishBtn print_edit_page" name="add_item" value="Save">
            <?php if (!empty($item)) { ?>
                <input type="submit" class="button_small whitishBtn print_edit_page" name="save_next" value="Save and Next >">
<?php } ?>
            <a href="/admin/print/items" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>

</form>
