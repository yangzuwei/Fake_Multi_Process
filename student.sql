CREATE TABLE `student` (
  `id_num` varchar(255) NOT NULL COMMENT '身份证号码',
  `std_name` varchar(255) NOT NULL COMMENT '姓名',
  `aux_num` varchar(16) NOT NULL COMMENT '学籍辅号',
  `school` varchar(72) NOT NULL COMMENT '学校',
  `class` int(7) NOT NULL COMMENT '班级',
  `is_handle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已经处理',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
