# Requerimientos no funcionales (línea base)

Vienen del documento funcional de Desarrollo de Software I, sección 2.2. Los RNF-08 a RNF-11 salieron del trabajo de despliegue (épica #5); los nuevos siguen desde el RNF-12.

| ID | Categoría | Descripción | Cómo está hoy en el código |
|---|---|---|---|
| RNF-01 | Usabilidad | La interfaz debe ser intuitiva y estar optimizada para navegación desde dispositivos móviles, dado que la mayoría de los usuarios acceden desde celular. | Por verificar |
| RNF-02 | Disponibilidad | El sistema debe estar disponible al menos el 95 % del tiempo, permitiendo a los compradores consultar el catálogo en cualquier momento del día. | Depende del despliegue. Mientras sea una demostración en un hosting gratuito se mide como «mejor esfuerzo»; el 95 % aplica a un despliegue en producción. La configuración centralizada de rutas (`APP_URL` y `BASE_PATH`, Issue #10) facilita cambiar la ubicación de instalación, pero no garantiza la disponibilidad requerida. |
| RNF-03 | Seguridad | Las contraseñas deben almacenarse cifradas mediante hash. El acceso a funcionalidades restringidas debe validarse según el rol del usuario autenticado. | Se usa `password_hash` / `password_verify`, `requiereRol()` y protección CSRF (`includes/csrf.php`) |
| RNF-04 | Rendimiento | El sistema debe retornar resultados de búsqueda de locales y productos en un tiempo máximo de 3 segundos bajo condiciones normales de uso. | Sin medición |
| RNF-05 | Escalabilidad | La arquitectura debe permitir incorporar nuevos módulos o funcionalidades sin afectar el núcleo del sistema. | Código organizado por módulos (una carpeta por módulo). Las rutas se centralizaron mediante `APP_URL` y `BASE_PATH` en `includes/config.php` (Issue #10), permitiendo instalar la aplicación en distintas carpetas o dominios sin modificar los enlaces de cada módulo. |
| RNF-06 | Compatibilidad | El sistema debe funcionar correctamente en Chrome, Firefox, Safari, Opera y Edge, tanto en móvil como en escritorio. | Sin pruebas registradas |
| RNF-07 | Mantenibilidad | El código debe estar documentado y organizado en módulos independientes para facilitar su mantenimiento y actualización. | Módulos por carpeta; documentación en `docs/` |
| RNF-08 | Seguridad | Las credenciales de la base de datos y cualquier otro secreto deben obtenerse de la configuración del servidor, nunca del código ni del repositorio. | Pendiente (#21): `includes/config.php` trae el usuario `root` sin contraseña |
| RNF-09 | Disponibilidad | Debe haber copias de seguridad periódicas de la base de datos y un procedimiento probado para restaurarlas. | Pendiente (#26) |
| RNF-10 | Seguridad | El sitio debe servirse solo por HTTPS, con cookies de sesión seguras. | Pendiente (#22): la cookie de sesión no lleva la marca `secure` |
| RNF-11 | Compatibilidad | El sistema debe funcionar igual en servidores Linux y Windows. | Pendiente (#23): las carpetas y las tablas con mayúscula inicial fallan en Linux |
