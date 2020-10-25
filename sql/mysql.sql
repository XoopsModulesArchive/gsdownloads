# phpMyAdmin MySQL-Dump
# version 2.2.2
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# --------------------------------------------------------
#
# Table structure for table `gsdownloads_broken`
#
#CREATE TABLE gsdownloads_broken (
# reportid int(5) NOT NULL auto_increment,
# lid int(11) NOT NULL default '0',
# sender int(11) NOT NULL default '0',
# ip varchar(20) NOT NULL default '',
# PRIMARY KEY (reportid),
# KEY lid (lid),
# KEY sender (sender),
# KEY ip (ip)
#) ENGINE = ISAM;
# --------------------------------------------------------
#
# Table structure for table `gsdownloads_cat`
#
CREATE TABLE gsdownloads_cat (
    cid    INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    pid    INT(5) UNSIGNED NOT NULL DEFAULT '0',
    title  VARCHAR(50)     NOT NULL DEFAULT '',
    imgurl VARCHAR(150)    NOT NULL DEFAULT '',
    PRIMARY KEY (cid),
    KEY pid (pid)
)
    ENGINE = ISAM;
# --------------------------------------------------------
#
# Table structure for table `gsdownloads_downloads`
#
CREATE TABLE gsdownloads_downloads (
    lid       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    cid       INT(5) UNSIGNED  NOT NULL DEFAULT '0',
    title     VARCHAR(100)     NOT NULL DEFAULT '',
    url       VARCHAR(250)     NOT NULL DEFAULT '',
    homepage  VARCHAR(100)     NOT NULL DEFAULT '',
    version   VARCHAR(10)      NOT NULL DEFAULT '',
    size      INT(8)           NOT NULL DEFAULT '0',
    platform  VARCHAR(50)      NOT NULL DEFAULT '',
    logourl   VARCHAR(60)      NOT NULL DEFAULT '',
    submitter INT(11)          NOT NULL DEFAULT '0',
    status    TINYINT(2)       NOT NULL DEFAULT '0',
    date      INT(10)          NOT NULL DEFAULT '0',
    hits      INT(11) UNSIGNED NOT NULL DEFAULT '0',
    rating    DOUBLE(6, 4)     NOT NULL DEFAULT '0.0000',
    votes     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    comments  INT(11) UNSIGNED NOT NULL DEFAULT '0',
    price     VARCHAR(10)      NOT NULL DEFAULT 'free',
    PRIMARY KEY (lid),
    KEY cid (cid),
    KEY status (status),
    KEY title (title(40))
)
    ENGINE = ISAM;
# --------------------------------------------------------
#
# Table
