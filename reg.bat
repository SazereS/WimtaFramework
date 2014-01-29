@echo off
rem Добавляем путь до папки в переменную Path
setx Path "%Path%;%cd%"
setx Wimta_Path "%cd%"
pause