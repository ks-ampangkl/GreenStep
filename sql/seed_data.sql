-- seed_data.sql
USE greenstep_api;

SET FOREIGN_KEY_CHECKS=0;
DELETE FROM eco_photos;
DELETE FROM goals;
DELETE FROM friend_requests;
DELETE FROM friendships;
DELETE FROM challenge_members;
DELETE FROM challenges;
DELETE FROM tips;
DELETE FROM activity_logs;
DELETE FROM activity_types;
DELETE FROM users;
SET FOREIGN_KEY_CHECKS=1;

-- bcrypt hash reused for all demo users
SET @HASH='$2y$10$UcA7cpCFTcsmiUfDehUmQ.W.bCCjqG3LUvCZ9JIwDvFCcQ1aiWkKG';

INSERT INTO users(name,email,password_hash,role,eco_points,gained_today) VALUES
('Admin','admin@greenstep.test',@HASH,'admin',0,0),
('John Tan','john@test.com',@HASH,'member',520,25),
('Sarah Lee','sarah@test.com',@HASH,'member',470,20),
('Alex Lim','alex@test.com',@HASH,'member',390,18),
('Michael Ong','michael@test.com',@HASH,'member',315,15),
('Emily Chen','emily@test.com',@HASH,'member',280,10),
('Daniel Ng','daniel@test.com',@HASH,'member',240,8),
('Aisyah Ahmad','aisyah@test.com',@HASH,'member',210,6),
('Farhan Ismail','farhan@test.com',@HASH,'member',180,4),
('Grace Tan','grace@test.com',@HASH,'member',150,3);

INSERT INTO activity_types(category,name,unit,kg_co2_per_unit,info) VALUES
('Transport','Car (Petrol)','km',0.21,'Petrol car'),
('Transport','Bus','km',0.08,'Bus'),
('Transport','Bicycle','km',0.00,'Bike'),
('Energy','Electricity','kWh',0.58,'Electricity'),
('Waste','Recycle Plastic','kg',-1.50,'Recycle'),
('Food','Beef','meal',5.00,'Beef');

INSERT INTO activity_logs(user_id,activity_type_id,amount,emissions_kg,logged_date) VALUES
(2,1,10,2.10,CURDATE()),
(2,3,5,0,CURDATE()),
(3,2,15,1.20,CURDATE()),
(4,4,8,4.64,CURDATE()),
(5,5,3,-4.50,CURDATE()),
(6,6,1,5.00,CURDATE()),
(7,1,20,4.20,CURDATE()),
(8,2,10,0.80,CURDATE()),
(9,3,12,0,CURDATE()),
(10,5,5,-7.50,CURDATE());

INSERT INTO tips(title,body,category) VALUES
('Walk More','Walk short distances instead of driving.','Transport'),
('Switch Off Lights','Reduce electricity usage.','Energy'),
('Recycle','Separate recyclable waste.','Waste');

INSERT INTO challenges(title,description,target_type) VALUES
('Bike Week','Cycle 50 km together','transport'),
('Zero Waste','Reduce household waste','waste');

INSERT INTO challenge_members VALUES
(1,2),(1,3),(1,4),(2,5),(2,6);

INSERT INTO friendships VALUES
(2,3),(3,2),
(2,4),(4,2),
(5,6),(6,5);

INSERT INTO friend_requests(sender_id,receiver_id,requested_at) VALUES
(7,2,'2026-06-25 09:00'),
(8,2,'2026-06-25 10:00'),
(2,9,'2026-06-25 11:00');

INSERT INTO goals(user_id,title,target_to_reduce_kg,duration,start_date,emissions_reduced_so_far_kg) VALUES
(2,'Reduce 20kg CO2',20,'30 days',CURDATE(),5),
(3,'Bike to Work',15,'30 days',CURDATE(),4),
(4,'Recycle More',10,'14 days',CURDATE(),2);

INSERT INTO eco_photos(user_id,image_url,achievement,uploaded_on) VALUES
(2,'uploads/photos/john_bike.jpg','Cycled to work',CURDATE()),
(3,'uploads/photos/sarah_tree.jpg','Tree planting',CURDATE()),
(4,'uploads/photos/alex_recycle.jpg','Recycled bottles',CURDATE());
