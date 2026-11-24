# Requerimientos no funcionales (línea base)

Vienen del documento funcional de Desarrollo de Software I, sección 2.2. Los RNF-08 a RNF-11 salieron del trabajo de despliegue (épica #5) y el RNF-12, de la revisión de la interfaz (épica #49); los nuevos siguen desde el RNF-13.

| ID | Categoría | Descripción | Cómo está hoy en el código |
|---|---|---|---|
| RNF-01 | Usabilidad | La interfaz debe ser intuitiva y estar optimizada para navegación desde dispositivos móviles, dado que la mayoría de los usuarios acceden desde celular. | Por verificar |
| RNF-02 | Disponibilidad | El sistema debe estar disponible al menos el 95 % del tiempo, permitiendo a los compradores consultar el catálogo en cualquier momento del día. | Depende del despliegue. Mientras sea una demostración en un hosting gratuito se mide como «mejor esfuerzo»; el 95 % aplica a un despliegue en producción |
| RNF-03 | Seguridad | Las contraseñas deben almacenarse cifradas mediante hash. El acceso a funcionalidades restringidas debe validarse según el rol del usuario autenticado. | Se usa `password_hash` / `password_verify`, `requiereRol()` y protección CSRF (`includes/csrf.php`) |
| RNF-04 | Rendimiento | El sistema debe retornar resultados de búsqueda de locales y productos en un tiempo máximo de 3 segundos bajo condiciones normales de uso. | Sin medición |
| RNF-05 | Escalabilidad | La arquitectura debe permitir incorporar nuevos módulos o funcionalidades sin afectar el núcleo del sistema. | Código organizado por módulos (una carpeta por módulo) |
| RNF-06 | Compatibilidad | El sistema debe funcionar correctamente en Chrome, Firefox, Safari, Opera y Edge, tanto en móvil como en escritorio. | Sin pruebas registradas |
| RNF-07 | Mantenibilidad | El código debe estar documentado y organizado en módulos independientes para facilitar su mantenimiento y actualización. | Módulos por carpeta; documentación en `docs/` |
| RNF-08 | Seguridad | Las credenciales de la base de datos y cualquier otro secreto deben obtenerse de la configuración del servidor, nunca del código ni del repositorio. | Pendiente (#21): `includes/config.php` trae el usuario `root` sin contraseña |
| RNF-09 | Disponibilidad | Debe haber copias de seguridad periódicas de la base de datos y un procedimiento probado para restaurarlas. | Pendiente (#26) |
| RNF-10 | Seguridad | El sitio debe servirse solo por HTTPS, con cookies de sesión seguras. | Pendiente (#22): la cookie de sesión no lleva la marca `secure` |
| RNF-11 | Compatibilidad | El sistema debe funcionar igual en servidores Linux y Windows. | Sí (#16): el módulo de locales está en `local/` y las consultas usan los nombres de tabla en minúscula, igual que `database/galezarzalBD.sql`. Probado en Linux con MariaDB sin `lower_case_table_names`. Falta el prefijo `/galezarzal/` escrito a mano (#10) |
| RNF-12 | Accesibilidad | El sitio debe cumplir el nivel AA de WCAG 2.2 en contraste (4,5:1 para texto normal y 3:1 para texto grande y bordes de controles), tamaño de letra (16 px el texto normal y nunca menos de 13 px), foco visible con teclado y tamaño de los elementos que se tocan (al menos 44 px de alto en el celular). Se verifica con Lighthouse: 90 o más en Accesibilidad en inicio, locales, local, producto y login. | Pendiente (#50): el rojo `#E94560` sobre blanco da 3,8:1 y el gris `#888780`, 3,6:1; hay textos de 11,5 a 13,6 px y los campos no muestran el foco. Lo cumplen el #65 y el #69 |
