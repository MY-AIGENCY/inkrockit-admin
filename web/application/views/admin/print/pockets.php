<a class="button_big" href="<?= $_SERVER['REQUEST_URI'] ?>/add"><span class="iconsweet">+</span>Add new Pocket</a>

<div class="one_wrap">

    <div class="widget" style="width: 900px">
        <div class="widget_body">
            <table class="activity_datatable" width="100%" id="sortTable">
                <thead> 
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($pockets)) {
                        foreach ($pockets as $pocket) {
                            ?>
                            <tr>
                                <td><a href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $pocket['id'] ?>"><?= $pocket['title'] ?></a></td>
                                <td><?= $pocket['type'] ?></td>
                                <td width="50"><?= ($pocket['active']) ? 'Yes' : 'No' ?></td>
                                <td width="50">
                                    <a class="ui-icon ui-icon-pencil left" href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $pocket['id'] ?>">edit</a>
                                    <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="<?= $_SERVER['REQUEST_URI'] ?>/del/<?= $pocket['id'] ?>">del</a>
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