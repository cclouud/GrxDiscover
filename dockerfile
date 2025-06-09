# Usa una imagen oficial de PHP con servidor embebido
FROM php:8.2-cli

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia todo tu proyecto (la carpeta actual) al contenedor
COPY . .

# Expón el puerto que Render espera por defecto
EXPOSE 10000

# Comando para iniciar el servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:10000"]
