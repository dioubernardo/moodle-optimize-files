# Prerequisites

[libjpeg-turbo](https://libjpeg-turbo.org/) for optimize JPEG

[optipng](http://optipng.sourceforge.net/) for optimize PNG

[FFmpeg](https://ffmpeg.org/) for optimize MP4

[Ghostscript](https://www.ghostscript.com/) for optimize PDF

```
sudo apt install libjpeg-turbo-progs optipng ffmpeg ghostscript
```

# How to install

```
cd [path/to/moodle]

git clone https://github.com/dioubernardo/moodle-optimize-files local/optimizer
rm -rf local/optimizer/.git

chown -R www-data:www-data local/optimizer
find local/optimizer -type d -print0 | xargs -0 chmod 750
find local/optimizer -type f -print0 | xargs -0 chmod 640

cat <<EOF | crontab -
`crontab -l`
* * * * * sudo -u www-data /usr/bin/php [path/to/moodle]/local/optimizer/cli/cron.php
EOF
```
Access https://yourmoodledomain.com/admin/index.php to finish instalation

# Memories

[MDL-70832 - Possibility of optimizing files in the background](https://tracker.moodle.org/browse/MDL-70832)

[MDL-70939 - Incorrect downsize image](https://tracker.moodle.org/browse/MDL-70939)

# SQL to include existing files to be optimized

```
insert ignore into mdl_optimizer_files
select distinct
    contenthash,
    0
from
    mdl_files
where
    mimetype in ("video/mp4", "application/pdf", "image/png", "image/jpeg") and
    component not in ("assignfeedback_editpdf", "core", "core_admin") and
    not (component="user" and filearea="icon") and
    not (component="user" and filearea="draft")
```