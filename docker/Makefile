perm:
	sudo chgrp -R ${USER} ./
	sudo chmod -R ug+rwx ./
	sudo find ./ -type d -exec chmod 777 {} \;
	sudo find ./ -type f -exec chmod 666 {} \;
	sudo find ../ -type d -exec chmod 775 {} \;
	sudo find ../ -type f -exec chmod 664 {} \;
	sudo chown -R sysuser:www-data ../
	sudo chmod -R 775 ../
	sudo chown -R sysuser:www-data ./
	sudo chmod -R 775 ./

up:
	docker-compose up -d

build:
	docker-compose up --build -d

down:
	docker-compose down

