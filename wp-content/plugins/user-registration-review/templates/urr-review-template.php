
    <div id="review-grid">
        <div class="review-table-header">
            <label for="select-rating"></label>
            <select name="filter-rating" id="select-rating">
                <?php for ($i = 0; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i == 0 ? '' : $i ?></option>
                <?php endfor; ?>
            </select>
            <label for=""><?= __("Filter Rating", "user-registration-review") ?></label>
        </div>
        <div class="review-table">
            <table id="review-table">
                <thead>
                <tr>
                    <th><?= __("User Email", "user-registration-review") ?></th>
                    <th><?= __("Full Name", "user-registration-review") ?></th>
                    <th><?= __("Review", "user-registration-review") ?></th>
                    <th><?= __("Rating", "user-registration-review") ?></th>
                </tr>
                </thead>
                <?php
                echo '<tbody>';
                foreach ($reviews as $review) {
                    echo '<tr>';
                    echo '<td>' . esc_html($review['user_login']) . '</td>';
                    echo '<td>' . esc_html($review['user_email']) . '</td>';
                    echo '<td>' . esc_html($review['review']) . '</td>';
                    echo '<td>' . esc_html($review['rating']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                ?>
            </table>
        </div>
        <div class="review-table-footer">
            <div id="pagination-block">
                <div class="pagination-left">
                    <p><?= __("Total Entries: ") . $totalCount ?></p>
                </div>
                <div class="pagination-right">
                    <?php for ($i = 1; $i <= ceil($totalCount / 5); $i++): ?>
                        <span class="pagination-item <?= ($i == 1) ? 'active' : '' ?>"><?= $i; ?></span>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
