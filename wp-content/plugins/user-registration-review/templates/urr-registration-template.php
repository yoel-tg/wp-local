<div class="user-registration-review-container">
	<div id="user-registration-review-form">
		<form id="registration-review-form">
			<label for="user-email"><?= __("User Email", "user-registration-review") ?></label>
			<input type="email" id="user-email" name="user_email" required>

			<label for="password"><?= __("Password", "user-registration-review") ?></label>
			<input type="password" id="password" name="password" required>

			<label for="first-name"><?= __("First Name", "user-registration-review") ?></label>
			<input type="text" id="first-name" name="first_name" required>

			<label for="last-name"><?= __("Last Name", "user-registration-review") ?></label>
			<input type="text" id="last-name" name="last_name" required>

			<label for="review"><?= __("Review", "user-registration-review") ?></label>
			<textarea id="review" name="review" required></textarea>

			<label for="rating"><?= __("Rating", "user-registration-review") ?></label>
			<input type="number" id="rating" name="rating" min="1" max="5" required>

			<button type="submit"><?= __("Submit", "user-registration-review") ?></button>
		</form>
	</div>
</div>