CREATE TABLE `student` (
  `id_num` char(32) NOT NULL COMMENT '身份证号码',
  `std_name` char(64) NOT NULL COMMENT '姓名',
  `aux_num` char(16) NOT NULL COMMENT '学籍辅号',
  `school` char(32) NOT NULL COMMENT '学校',
  `class` char(16) NOT NULL COMMENT '班级',
  `is_handle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已经处理',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_num`),
  UNIQUE KEY `id_num` (`id_num`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
