CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user` varchar(254) NOT NULL,
 `username` varchar(50) NOT NULL,
 `password` char(255) NOT NULL,
 `is_2fa_enabled` tinyint(1) NOT NULL DEFAULT 0,
 `reg_date` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`),
 UNIQUE KEY `user` (`user`),
 UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci


CREATE TABLE `workout_days` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `workout_date` date NOT NULL,
 PRIMARY KEY (`id`),
 KEY `user_id` (`user_id`),
 CONSTRAINT `workout_days_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci




CREATE TABLE `workout_logs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `workout_day_id` int(11) NOT NULL,
 `exercise` varchar(255) NOT NULL,
 `sets` int(11) NOT NULL,
 `reps` int(11) NOT NULL,
 `weight` float NOT NULL,
 PRIMARY KEY (`id`),
 KEY `user_id` (`user_id`),
 KEY `workout_day_id` (`workout_day_id`),
 CONSTRAINT `workout_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
 CONSTRAINT `workout_logs_ibfk_2` FOREIGN KEY (`workout_day_id`) REFERENCES `workout_days` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
