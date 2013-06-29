<?php echo $header; ?>

			<h1>Editing &ldquo;<?php echo Str::limit($category->title, 4); ?>&rdquo;</h1>

			<?php echo $messages; ?>

			<section class="content">

				<form method="post" action="<?php echo Uri::to('categories/edit/' . $category->id); ?>" novalidate>

					<input name="token" type="hidden" value="<?php echo $token; ?>">

					<fieldset>
						<p>
							<label for="title"><?php echo __('categories.title', 'Title'); ?>:</label>
							<input id="title" name="title" value="<?php echo Input::previous('title', $category->title); ?>">

							<em><?php echo __('categories.title_explain', 'Your category title.'); ?></em>
						</p>

						<p>
							<label for="slug"><?php echo __('categories.slug', 'Slug'); ?>:</label>
							<input id="slug" name="slug" value="<?php echo Input::previous('slug', $category->slug); ?>">

							<em><?php echo __('categories.slug_explain', 'The slug for your category.'); ?></em>
						</p>

						<p>
							<label for="description"><?php echo __('categories.description', 'Description'); ?>:</label>
							<textarea id="description" name="description"><?php echo Input::previous('description', $category->description); ?></textarea>

							<em><?php echo __('categories.description_explain', 'A brief outline of what your category is about.'); ?></em>
						</p>
					</fieldset>

					<p class="buttons">
						<button type="submit"><?php echo __('categories.save', 'Save'); ?></button>
						<a href="<?php echo Uri::to('categories'); ?>"><?php echo __('categories.return_categories', 'Return to categories'); ?></a>
					</p>

				</form>
			</section>

<?php echo $footer; ?>