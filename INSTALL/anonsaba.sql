--
-- Table structure for table `ads`
--

CREATE TABLE IF NOT EXISTS `PREFIXads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `type` text NOT NULL,
  `w` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE IF NOT EXISTS `PREFIXbans` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `ip` varchar(25) NOT NULL,
  `reason` text NOT NULL,
  `until` int(11) NOT NULL,
  `boards` text NOT NULL,
  `appeal` int(11) DEFAULT NULL,
  `appealed` int(11) DEFAULT NULL,
  `appealmsg` text NOT NULL,
  `deny` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE IF NOT EXISTS `PREFIXboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(9999) NOT NULL,
  `desc` varchar(9999) NOT NULL,
  `class` varchar(9999) NOT NULL,
  `section` varchar(9999) NOT NULL,
  `header` varchar(9999) NOT NULL,
  `imagesize` int(11) NOT NULL DEFAULT '1024000',
  `postperpage` int(255) NOT NULL DEFAULT '8',
  `boardpages` int(11) NOT NULL DEFAULT '11',
  `threadhours` int(11) NOT NULL,
  `markpage` int(11) NOT NULL DEFAULT '9',
  `threadreply` int(11) NOT NULL DEFAULT '0',
  `postername` varchar(9999) NOT NULL DEFAULT 'Anonymous',
  `locked` int(11) NOT NULL DEFAULT '0',
  `email` int(11) NOT NULL DEFAULT '1',
  `ads` int(11) NOT NULL DEFAULT '1',
  `showid` int(11) NOT NULL DEFAULT '0',
  `report` int(11) NOT NULL DEFAULT '1',
  `captcha` int(11) NOT NULL DEFAULT '0',
  `forcedanon` int(11) NOT NULL DEFAULT '0',
  `trial` int(11) NOT NULL DEFAULT '0',
  `popular` int(11) NOT NULL DEFAULT '0',
  `recentpost` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `board_filetypes`
--

CREATE TABLE IF NOT EXISTS `PREFIXboard_filetypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `boardid` int(11) NOT NULL,
  `fileid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `expiredbans`
--

CREATE TABLE IF NOT EXISTS `PREFIXexpiredbans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` mediumtext NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `PREFIXfiles` (
  `id` int(11) NOT NULL,
  `board` varchar(999) NOT NULL,
  `file` varchar(999) NOT NULL,
  `md5` varchar(999) NOT NULL,
  `type` varchar(999) NOT NULL,
  `original` varchar(999) NOT NULL,
  `size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `filetypes`
--

CREATE TABLE IF NOT EXISTS `PREFIXfiletypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(999) NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `front`
--

CREATE TABLE IF NOT EXISTS `PREFIXfront` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordr` int(11) NOT NULL,
  `type` varchar(999) NOT NULL,
  `by` varchar(999) NOT NULL,
  `subject` varchar(999) NOT NULL,
  `email` varchar(999) NOT NULL,
  `message` varchar(9999) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `PREFIXlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(999) NOT NULL,
  `message` varchar(999) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `PREFIXposts` (
  `id` int(11) NOT NULL,
  `name` varchar(9999) NOT NULL,
  `email` varchar(9999) NOT NULL,
  `subject` varchar(9999) NOT NULL,
  `message` varchar(9999) NOT NULL,
  `password` varchar(999) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL,
  `ip` varchar(9999) NOT NULL,
  `boardname` varchar(9999) NOT NULL,
  `time` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `ipid` varchar(999) NOT NULL,
  `sticky` int(11) NOT NULL DEFAULT '0',
  `lock` int(11) NOT NULL DEFAULT '0',
  `deleted_time` int(12) NOT NULL,
  `bumped` int(12) NOT NULL,
  `cleared` int(11) NOT NULL DEFAULT '0',
  `report` int(11) NOT NULL,
  `reportmsg` text NOT NULL,
  `banmessage` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `PREFIXsections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `abbr` varchar(999) NOT NULL,
  `name` varchar(999) NOT NULL,
  `hidden` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_config`
--

CREATE TABLE IF NOT EXISTS `PREFIXsite_config` (
  `config_name` varchar(999) NOT NULL,
  `config_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `PREFIXstaff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(999) NOT NULL,
  `password` varchar(999) NOT NULL,
  `sessionid` varchar(999) NOT NULL,
  `active` int(11) NOT NULL,
  `level` varchar(999) NOT NULL,
  `suspended` int(11) NOT NULL,
  `boards` mediumtext NOT NULL,
  `failed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wordfilters`
--

CREATE TABLE IF NOT EXISTS `PREFIXwordfilters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(9999) NOT NULL,
  `replace` varchar(9999) NOT NULL,
  `boards` varchar(9999) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

