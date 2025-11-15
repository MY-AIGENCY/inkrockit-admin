<a class="button_big" href="<?= $_SERVER['REQUEST_URI'] ?>/add"><span class="iconsweet">+</span>Add new Product Type</a>

<div class="one_wrap">

    <div class="widget" style="width: 800px">
        <div class="widget_body">
            <table class="activity_datatable" width="800" id="sortTable">
                <thead> 
                    <tr>
                        <th>Title</th>
                        <th>ABBR</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($cats)) {
                        foreach ($cats as $cat) {
                            ?>
                            <tr>
                                <td><a href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $cat['id'] ?>"><?= $cat['title'] ?></a></td>
                                <td><a href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $cat['id'] ?>"><?= $cat['abbr'] ?></a></td>
                                <td width="50"><?= ($cat['active']) ? 'Yes' : 'No' ?></td>
                                <td width="50">
                                    <a class="ui-icon ui-icon-pencil left" href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $cat['id'] ?>">edit</a>
                                    <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="<?= $_SERVER['REQUEST_URI'] ?>/del/<?= $cat['id'] ?>">del</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#sortTable").tablesorter({headers: {
                2: {sorter: false}
            }});
    });
</script>