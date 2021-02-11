# Prerequisites

[optipng](http://optipng.sourceforge.net/) for optimize PNG

[FFmpeg](https://ffmpeg.org/) for optimize MP4

[Ghostscript](https://www.ghostscript.com/) for optimize PDF

```
sudo apt install optipng ffmpeg ghostscript
```

[mozjpeg](https://github.com/mozilla/mozjpeg)

```
apt install cmake autoconf automake libtool nasm make pkg-config git zlib1g-dev libpng-dev
cd ~
git clone https://github.com/mozilla/mozjpeg.git
cd mozjpeg
mkdir build && cd build
sudo cmake -G"Unix Makefiles" -DPNG_SUPPORTED=ON ../
sudo make install
cd ~
rm -rf mozjpeg
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
