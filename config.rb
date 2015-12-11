require 'compass/import-once/activate'
# Require any additional compass plugins here.

http_path = "webroot"
css_dir = "./webroot/assets/css"
sass_dir = "./webroot/sources/scss"
images_dir = "./webroot/sources/images"
javascripts_dir = "./webroot/assets/js"
fonts_dir = "./webroot/assets/fonts"

additional_import_paths = [
	'./webroot/sources/bower_components/bootstrap/scss',
	'./webroot/sources/bower_components/fontawesome/scss'
]
sass_options = {:unix_newlines => true}
output_style = :compressed

relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass scss scss && rm -rf sass && mv scss sass
