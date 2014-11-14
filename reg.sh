#!/bin/sh
# Добавление текущей директории к переменной PATH
export PATH=$PATH:$PWD
export Wimta_Path=$PWD
# Делаю консольный помошник исполняемым
chmod +x wimta.sh
echo "Finished! You just successfully registered Wimta's path! Press any key to exit..."
read -n 1 -s
