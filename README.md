# Prerequisites

[guetzli](https://github.com/google/guetzli) for optimize JPEG
```
sudo apt install guetzli
```

[optipng](http://optipng.sourceforge.net/) for optimize PNG
```
sudo apt install optipng
```

[FFmpeg](https://ffmpeg.org/) for optimize MP4
```
sudo apt install ffmpeg
```

[Ghostscript](https://www.ghostscript.com/) for optimize PDF
```
sudo apt install ghostscript 
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
* * * * * sudo -u www-data /usr/bin/php [path/to/moodle]/local/optimizer/cli/cron.php >/dev/null
EOF
```
Access https://yourmoodledomain.com/admin/index.php to finish instalation

# Memories

[MDL-70832](https://tracker.moodle.org/browse/MDL-70832)
