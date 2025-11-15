<div id="activity_stats">
    <h3>Edit Item</h3>
</div> 

<form method="POST" name="item_edit">
    <input type="hidden" value="<?= $item['id'] ?>" name="item_id">
    <input type="hidden" value="<?= $item['item_id'] ?>" name="is_item_id">

    <div class="msgbar msg_Error hide"><p></p></div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">General</a></li>
            <li><a href="#tabs-2">Size</a></li>
            <li><a href="#tabs-3">Paper</a></li>
            <li><a href="#tabs-4">Coating</a></li>
            <li><a href="#tabs-5">Finishes</a></li>
            <li><a href="#tabs-6">Comment</a></li>
        </ul>
        <div id="tabs-1">

            <ul class="form_fields_container" style="width: 800px">
                <li>
                    <div class="items_img">
                        <?php
                        foreach ($item['imgs'] as $img) {
                            ?>
                            <div>
                                <a class="delete ui-icon ui-icon-closethick" data-img="<?= $img['img'] ?>"></a>
                                <img src="/files/items/thumbnails/<?= $img['img'] ?>.png" width="100%">
                            </div>
                            <?php
                        }
                        ?><br class="clear">
                    </div>
                    <br class="clear">
                    <label>Add New Images</label>
                    <div class="form_input">
                        <input id="fileupload_add" type="file" name="files[]" multiple="">
                        <div id="progressbar" class="right" style="margin-right: 20px; display: none"><span></span></div>

                        <div id="input_uploads" style="display: block; width: 90%; display: none;"></div>
                    </div>
                    <br class="clear">
                    <label>Industries</label>
                    <div class="form_input"><p><?= $item['type_title'] ?></p></div>
                    <br class="clear">
                    <label>Index</label>
                    <div class="form_input"><p><?= $item['type_index'] ?></p></div>
                    <br class="clear">
                    <label>Quantity</label>
                    <div class="form_input"><input type="text" name="count" value="<?= $item['count'] ?>"></div>
                    <br class="clear">
                    <label>Job #</label>
                    <div class="form_input"><input type="text" name="job_id" value="<?= $item['job_id'] ?>"></div>
                    <br class="clear">
                    <label>Sample Name</label>
                    <div class="form_input"><input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>"></div>
                    <br class="clear">
                    <label>Weight, lbs</label>
                    <div class="form_input"><input type="text" name="weight" value="<?= htmlspecialchars($item['weight']) ?>"></div>
                    <br class="clear">
                    <label>Product Type</label>
                    <div class="form_input">
                        <select name="category" data-type="inspiration">
                            <option value="">Select Category</option>
                            <?php foreach ($cats as $cat) { ?>
                                <option value="<?= $cat['id'] ?>" <?= ($item['cat_id'] == $cat['id']) ? 'selected' : '' ?>><?= $cat['title'] ?></option>
                            <?php } ?>
                        </select>
                        <span class="ui-icon ui-icon-plus pointer" id="add_category" style="margin: 5px"></span>
                    </div>
                    <br class="clear">
                    <label>Design By Inkrockit?</label>
                    <div class="form_input">
                        <select name="client_item">
                            <option value="0">Yes</option>
                            <option value="1" <?= ($item['client_item']) ? 'selected="selected"' : '' ?>>No</option>
                        </select>
                    </div>
                    <br class="clear">
                    <label>Active:</label>
                    <div class="form_input">
                        <select name="active">
                            <option value="0">No</option>
                            <option value="1" <?= ($item['active']) ? 'selected="selected"' : '' ?>>Yes</option>
                        </select>
                    </div>
                    <br class="clear">
                </li>
            </ul>

        </div>

        <div id="tabs-2">

            <ul class="form_fields_container" style="width: 800px">
                <li>
                    <label><strong>Open/Flat size:</strong></label>
                    <br class="clear">
                    <label>Width:</label>
                    <div class="form_input">
                        <input type="text" name="dim_width[open]" class="left small" value="<?= htmlspecialchars(@$item['size']['open']['width']) ?>">
                    </div>
                    <br class="clear">
                    <label>Height:</label>
                    <div class="form_input">
                        <input type="text" name="dim_height[open]" class="left small" value="<?= htmlspecialchars(@$item['size']['open']['height']) ?>">
                    </div>
                    <br class="clear">
                    <label>ABBR:</label>
                    <div class="form_input">
                        <input type="text" name="dim_abbr[open]" class="left small" value="<?= htmlspecialchars(@$item['size']['open']['abbr']) ?>">
                    </div>
                    <br class="clear">
                    <label>Description:</label>
                    <div class="form_input">
                        <textarea name="dim_descr[open]" style="width: 496px !important;" class="left"><?= @$item['size']['open']['description'] ?></textarea>
                    </div>
                </li>
                <li>
                    <label><strong>Finished size:</strong></label>
                    <br class="clear">
                    <label>Width:</label>
                    <div class="form_input">
                        <input type="text" name="dim_width[flat]" class="left small" value="<?= htmlspecialchars(@$item['size']['flat']['width']) ?>">
                    </div>
                    <br class="clear">
                    <label>Height:</label>
                    <div class="form_input">
                        <input type="text" name="dim_height[flat]" class="left small" value="<?= htmlspecialchars(@$item['size']['flat']['height']) ?>">
                    </div>
                    <br class="clear">
                    <label>ABBR:</label>
                    <div class="form_input">
                        <input type="text" name="dim_abbr[flat]" class="left small" value="<?= htmlspecialchars(@$item['size']['flat']['abbr']) ?>">
                    </div>
                    <br class="clear">
                    <label>Description:</label>
                    <div class="form_input">
                        <textarea name="dim_descr[flat]" style="width: 496px !important;" class="left"><?= htmlspecialchars(@$item['size']['flat']['description']) ?></textarea>
                    </div>
                </li>
            </ul>
        </div>
        <div id="tabs-3">

            <ul class="form_fields_container">
                <li>
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_paper_field">
                        <span class="iconsweet">+</span> Add
                    </a>

                    <table class="activity_datatable small_datatable paper_db" width="100%" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
                        <tr>
                            <th width="84">Avaliable</th>
                            <th>Name</th>
                        </tr>
                        <?php
                        if (!empty($papers)) {
                            foreach ($papers as $val) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="use_paper[]" <?php if (!empty($papers_used) && in_array($val['id'], $papers_used)) echo 'checked="checked"'; ?> value="<?= $val['id'] ?>" class="short">
                                        <span class="ui-icon ui-icon-pencil pointer right edit_paper" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['description'] ?>" data-hasqtip="true"></span>
                                    </td>
                                    <td><?= $val['name'] ?></td>
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
                    
                    <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_coat_field">
                        <span class="iconsweet">+</span> Add
                    </a>
                    
                    <table class="activity_datatable small_datatable coat_db" width="100%" border="0" cellspacing="0" cellpadding="3">
                        <tr>
                            <th width="84">Avaliable</th>
                            <th>Name</th>
                        </tr>
                        <?php
                        if (!empty($coating)) {
                            foreach ($coating as $val) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="coating_aval[]" <?php if (!empty($coating_used) && in_array($val['id'], $coating_used)) echo 'checked="checked"'; ?> value="<?= $val['id'] ?>" class="short">
                                        <span class="ui-icon ui-icon-pencil pointer right edit_coating" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['abbr'] ?>" data-hasqtip="true"></span>
                                    </td>
                                    <td class="editable"><?= $val['title'] ?></td>
                                </tr>    
                                <?php
                            }
                        }
                        ?>
                        <tr class="hide">
                            <td colspan="2"><label>Name:</label>
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
                                    <input type="text" name="coat_count" class="small"> 
                                    <a class="button_small whitishBtn" id="add_coat">Add</a>
                                </div>
                                <br class="clear">
                            </td>
                        </tr>
                    </table>
                </li>
            </ul>

        </div>
        <div id="tabs-5">

            <a style="margin-bottom:10px;" class="whitishBtn button_small right" id="add_finish_field">
                <span class="iconsweet">+</span> Add
            </a>

            <ul class="form_fields_container">
                <li>

                    <table class="activity_datatable small_datatable finish_db" width="100%" border="0" cellspacing="0" cellpadding="3">
                        <tr>
                            <th width="84">Avaliable</th>
                            <th>Name</th>
                        </tr>
                        <?php
                        if (!empty($finishes)) {
                            foreach ($finishes as $val) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="finishes_aval[]" <?php if (!empty($finishes_used) && in_array($val['id'], $finishes_used)) echo 'checked="checked"'; ?> value="<?= $val['id'] ?>" class="short">
                                        <span class="ui-icon ui-icon-pencil pointer right edit_finish" data-id="<?= $val['id'] ?>"></span>
                                        <span class="ui-icon ui-icon-help pointer right qtips" title="<?= $val['abbr'] ?>" data-hasqtip="true"></span>
                                    </td>
                                    <td class="editable"><?= $val['title'] ?></td>
                                </tr>    
                                <?php
                            }
                        }
                        ?>
                        <tr class="hide">
                            <td colspan="2"><label>Name:</label>
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
                                    <input type="text" name="finish_count" class="small">
                                    <a class="button_small whitishBtn" id="add_finish">Add</a>
                                </div>
                                <br class="clear">
                            </td>
                        </tr>
                    </table>

                </li>
            </ul>

        </div>

        <div id="tabs-6">
            <div class="form_input">
                <textarea name="comment" style="height: 300px;"><?= @$item['comment'] ?></textarea>
            </div>
            <br class="clear">
        </div>
    </div>

    <ul class="form_fields_container submit_block">
        <li>
            <input class="button_small whitishBtn marg_l20" type="submit" value="Delete" name="delete" >
            <input class="button_small whitishBtn" type="submit" value="< Save and Prev" name="save_and_prev">
            <input class="button_small whitishBtn" type="submit" value="Save" name="just_save">
            <input class="button_small whitishBtn" type="submit" value="Save and Next >" name="save_and_next">
            <a href="/admin/inspiration/items" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>

</form>    