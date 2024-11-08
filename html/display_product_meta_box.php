<form method="POST" action="">
    <table style='width:100%;'>
        <thead>
            <tr>
                <th> ID</th>
                <th> Name</th>
                <th> Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class='table_body'>
            <?php

            if ((count($names) && count($typos)) != 0) {
                for ($i = 0; $i < $index - 1; $i++) {
            ?>
                    <tr class='row'>
                        <td class="id"></td>
                        <td><input type="text" name="name[]" value="<?php echo $names[$i] ?>" style="width:100%;" required /></td>
                        <td><input type="text" name="typo[]" value="<?php echo $typos[$i] ?>" style="width:100%;" required /></td>
                        <td>
                            <button type="button" class="add_more_button button">+</button>
                            <button type="button" class="remove_button button">-</button>
                        </td>
                    </tr>
                <?php
                }
                // echo "<pre>";
                // echo var_dump(get_field_object('book_product'));
            } else {
                ?>
                <tr class='row'>
                    <td class="id"></td>
                    <td><input type="text" name="name[]" style="width:100%;" required /></td>
                    <td><input type="text" name="typo[]" style="width:100%;" required /></td>
                    <td>
                        <button type="button" class="add_more_button button">+</button>
                        <button type="button" class="remove_button button">-</button>
                    </td>
                </tr>
            <?php
            } ?>

        </tbody>
    </table>

</form>