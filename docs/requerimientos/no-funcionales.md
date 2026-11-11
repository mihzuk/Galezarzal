# Requerimientos no funcionales (línea base)

Vienen del documento funcional de Desarrollo de Software I, sección 2.2. Los nuevos siguen desde el RNF-08.

| ID | Categoría | Descripción | Cómo está hoy en el código |
|---|---|---|---|
| RNF-01 | Usabilidad | La interfaz debe ser intuitiva y estar optimizada para navegación desde dispositivos móviles, dado que la mayoría de los usuarios acceden desde celular. | Por verificar |
| RNF-02 | Disponibilidad | El sistema debe estar disponible al menos el 95 % del tiempo, permitiendo a los compradores consultar el catálogo en cualquier momento del día. | Depende del despliegue |
| RNF-03 | Seguridad | Las contraseñas deben almacenarse cifradas mediante hash. El acceso a funcionalidades restringidas debe validarse según el rol del usuario autenticado. | Se usa `password_hash` / `password_verify`, `requiereRol()` y protección CSRF (`includes/csrf.php`) |
| RNF-04 | Rendimiento | El sistema debe retornar resultados de búsqueda de locales y productos en un tiempo máximo de 3 segundos bajo condiciones normales de uso. | Sin medición |
| RNF-05 | Escalabilidad | La arquitectura debe permitir incorporar nuevos módulos o funcionalidades sin afectar el núcleo del sistema. | Código organizado por módulos (una carpeta por módulo) |
| RNF-06 | Compatibilidad | El sistema debe funcionar correctamente en Chrome, Firefox, Safari, Opera y Edge, tanto en móvil como en escritorio. | Sin pruebas registradas |
| RNF-07 | Mantenibilidad | El código debe estar documentado y organizado en módulos independientes para facilitar su mantenimiento y actualización. | Módulos por carpeta; documentación en `docs/` |
