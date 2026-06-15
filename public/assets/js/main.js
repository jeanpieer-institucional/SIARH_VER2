// SIARH - Main JavaScript

// Configuración global
const getBaseUrl = () => {
  const path = window.location.pathname;
  const parts = path.split('/');
  if (parts.length > 1 && (parts[1].toLowerCase() === 'siarh_ver2' || parts[1].toLowerCase() === 'siarh')) {
    return '/' + parts[1];
  }
  return '';
};

const SIARH = {
  apiUrl: getBaseUrl(),
  theme: localStorage.getItem("theme") || "light",
};

// Inicialización
document.addEventListener("DOMContentLoaded", function () {
  initTheme();
  initSidebar();
  initAlerts();
  initForms();
  initSearch();
  checkSession();
  startIdleTimer();
});

// ==================== TEMA ====================
function initTheme() {
  document.documentElement.setAttribute("data-theme", SIARH.theme);

  const themeToggle = document.getElementById("theme-toggle");
  if (themeToggle) {
    themeToggle.addEventListener("click", toggleTheme);
  }
}

function toggleTheme() {
  SIARH.theme = SIARH.theme === "light" ? "dark" : "light";
  document.documentElement.setAttribute("data-theme", SIARH.theme);
  localStorage.setItem("theme", SIARH.theme);
}

// ==================== SIDEBAR ====================
function initSidebar() {
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const sidebar = document.querySelector(".sidebar");

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      sidebar.classList.toggle("active");

      // También ajustar el contenido principal
      const mainContent = document.querySelector(".main-content");
      if (mainContent) {
        mainContent.classList.toggle("active");
      }

      // Solo en móvil: Cerrar sidebar al hacer clic fuera
      if (window.innerWidth <= 768 && sidebar.classList.contains("active")) {
        setTimeout(() => {
          document.addEventListener("click", closeSidebarOnClickOutside);
        }, 100);
      }
    });
  }

  function closeSidebarOnClickOutside(e) {
    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
      sidebar.classList.remove("active");
      document.removeEventListener("click", closeSidebarOnClickOutside);
    }
  }

  // Marcar item activo
  const currentPath = window.location.pathname;
  document.querySelectorAll(".nav-item").forEach((item) => {
    const href = item.getAttribute("href");
    if (href && currentPath.includes(href)) {
      item.classList.add("active");
    }
  });
}

// ==================== ALERTAS ====================
function initAlerts() {
  // Auto-cerrar alertas después de 5 segundos
  setTimeout(() => {
    document.querySelectorAll(".alert").forEach((alert) => {
      fadeOut(alert);
    });
  }, 5000);
}

function showAlert(message, type = "info") {
  const alert = document.createElement("div");
  alert.className = `alert alert-${type}`;
  alert.innerHTML = `
        <i class="fas fa-${getAlertIcon(type)}"></i>
        <span>${message}</span>
    `;

  const container = document.querySelector(".content-wrapper");
  if (container) {
    container.insertBefore(alert, container.firstChild);
    setTimeout(() => fadeOut(alert), 5000);
  }
}

function getAlertIcon(type) {
  const icons = {
    success: "check-circle",
    error: "exclamation-circle",
    warning: "exclamation-triangle",
    info: "info-circle",
  };
  return icons[type] || "info-circle";
}

function fadeOut(element) {
  element.style.opacity = "0";
  element.style.transform = "translateY(-20px)";
  setTimeout(() => element.remove(), 300);
}

// ==================== FORMULARIOS ====================
function initForms() {
  // Validación en tiempo real
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault();
        showAlert("Por favor, complete todos los campos requeridos", "error");
      }
    });
  });

  // Auto-formateo de DNI
  document.querySelectorAll('input[name="dni"]').forEach((input) => {
    input.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 8);
    });
  });

  // Auto-formateo de teléfono
  document.querySelectorAll('input[type="tel"]').forEach((input) => {
    input.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 9);
    });
  });
}

function validateForm(form) {
  let isValid = true;

  form.querySelectorAll("[required]").forEach((field) => {
    if (!field.value.trim()) {
      field.style.borderColor = "var(--error)";
      isValid = false;
    } else {
      field.style.borderColor = "var(--border-color)";
    }
  });

  return isValid;
}

// ==================== BÚSQUEDA ====================
function initSearch() {
  const searchInput = document.getElementById("search-input");
  const btnSearch = document.getElementById("btn-search");

  // Solo activar búsqueda automática si NO hay botón de búsqueda manual
  if (searchInput && !btnSearch) {
    let timeout;
    searchInput.addEventListener("input", function () {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        performSearch(this.value);
      }, 300);
    });
  }
}

function performSearch(term) {
  // Si hay filtros seleccionados, permitir búsqueda vacía
  const hasFilters =
    document.getElementById("filter-estado")?.value ||
    document.getElementById("filter-carrera")?.value;

  if (!hasFilters && term.length < 2 && term.length > 0) return;

  // Implementar búsqueda según la página actual
  const path = window.location.pathname;

  if (path.includes("docentes")) {
    searchDocentes(term);
  } else if (path.includes("asistencias")) {
    searchAsistencias(term);
  }
}

function searchDocentes(term) {
  const filters = {
    estado: document.getElementById("filter-estado")?.value || "",
    carrera_id: document.getElementById("filter-carrera")?.value || "",
  };

  const tbody = document.querySelector("#docentes-table tbody");
  if (tbody) {
    tbody.style.opacity = "0.5";
  }

  fetch(
    `${SIARH.apiUrl}/docentes/search?term=${encodeURIComponent(term)}&estado=${filters.estado}&carrera_id=${filters.carrera_id}`,
  )
    .then(async (response) => {
      if (!response.ok) {
        const text = await response.text();
        throw new Error(`Server error: ${response.status} - ${text}`);
      }
      const text = await response.text();
      try {
        return JSON.parse(text);
      } catch (e) {
        throw new Error(`Invalid JSON: ${text.substring(0, 100)}...`);
      }
    })
    .then((data) => {
      if (tbody) tbody.style.opacity = "1";
      if (data.error) {
        throw new Error(data.error);
      }
      updateDocentesTable(data);
    })
    .catch((error) => {
      console.error("Error en búsqueda:", error);
      if (tbody) tbody.style.opacity = "1";
      showAlert("Error al realizar la búsqueda: " + error.message, "error");
    });
}

function updateDocentesTable(docentes) {
  const tbody = document.querySelector("#docentes-table tbody");
  if (!tbody) return;

  tbody.innerHTML = "";

  if (docentes.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="7" class="text-center">No se encontraron resultados</td></tr>';
    return;
  }

  docentes.forEach((docente) => {
    const row = `
            <tr>
                <td>${docente.codigo_empleado}</td>
                <td>${docente.nombres} ${docente.apellidos}</td>
                <td>${docente.dni}</td>
                <td>${docente.carrera_nombre || "N/A"}</td>
                <td>${docente.email}</td>
                <td><span class="badge badge-${getEstadoBadge(docente.estado)}">${docente.estado}</span></td>
                <td>
                    <a href="${SIARH.apiUrl}/docentes/edit/${docente.id}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteDocente(${docente.id})" class="btn btn-sm btn-error">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    tbody.innerHTML += row;
  });
}

function getEstadoBadge(estado) {
  const badges = {
    activo: "success",
    inactivo: "secondary",
    licencia: "warning",
  };
  return badges[estado] || "secondary";
}

// ==================== SESIÓN Y CONTROL DE INACTIVIDAD ====================
let idleTimer;
let warningTimer;
let countdownInterval;
const MAX_IDLE_TIME = 540000; // 9 minutos de inactividad (en ms) antes de la advertencia
const WARNING_DURATION = 60;   // 60 segundos de conteo regresivo en la advertencia

function checkSession() {
  // Verificar sesión en segundo plano cada 5 minutos
  setInterval(() => {
    const modal = document.getElementById('session-warning-modal');
    if (!modal || !modal.classList.contains('active')) {
      fetch(`${SIARH.apiUrl}/auth/check-session`)
        .then((response) => response.json())
        .then((data) => {
          if (!data.authenticated) {
            window.location.href = `${SIARH.apiUrl}/login?timeout=inactivity`;
          }
        })
        .catch((error) => console.error("Error verificando sesión:", error));
    }
  }, 300000); // 5 minutos
}

function startIdleTimer() {
  // El control de inactividad solo aplica cuando el usuario ha iniciado sesión (tiene la barra lateral/sidebar)
  if (!document.querySelector('.sidebar')) return;

  resetIdleTimer();

  // Escuchar eventos de interacción del usuario
  const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
  events.forEach(event => {
    document.addEventListener(event, resetIdleTimer, true);
  });
}

function resetIdleTimer() {
  clearTimeout(idleTimer);
  clearTimeout(warningTimer);
  clearInterval(countdownInterval);

  // Ocultar modal si existe
  const modal = document.getElementById('session-warning-modal');
  if (modal) {
    modal.classList.remove('active');
    setTimeout(() => {
      if (!modal.classList.contains('active')) {
        modal.style.display = 'none';
      }
    }, 300);
  }

  // Volver a activar los event listeners (por si fueron removidos en la advertencia)
  const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
  events.forEach(event => {
    document.removeEventListener(event, resetIdleTimer, true);
    document.addEventListener(event, resetIdleTimer, true);
  });

  // Programar la visualización de la advertencia
  idleTimer = setTimeout(showSessionWarning, MAX_IDLE_TIME);
}

function showSessionWarning() {
  // Remover los listeners mientras se muestra la advertencia, para obligar acción explícita
  const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
  events.forEach(event => {
    document.removeEventListener(event, resetIdleTimer, true);
  });

  let modal = document.getElementById('session-warning-modal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'session-warning-modal';
    modal.className = 'session-modal-overlay';
    modal.innerHTML = `
      <div class="session-modal-card">
        <div class="session-warning-icon session-pulse">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Su sesión está por expirar</h3>
        <p style="margin-bottom: var(--spacing-md); color: var(--text-secondary);">Ha estado inactivo. Por motivos de seguridad, su sesión se cerrará automáticamente.</p>
        <div class="session-countdown-wrapper">
          Tiempo restante:
          <span id="session-countdown" class="session-countdown-number">${WARNING_DURATION}</span>
          segundos
        </div>
        <div class="session-modal-actions">
          <button id="btn-extend-session" class="btn btn-primary">
            <i class="fas fa-sync"></i> Mantener Activo
          </button>
          <a href="${SIARH.apiUrl}/auth/logout" class="btn btn-secondary">
            <i class="fas fa-sign-out-alt"></i> Salir
          </a>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    document.getElementById('btn-extend-session').addEventListener('click', extendSession);
  }

  modal.style.display = 'flex';
  setTimeout(() => {
    modal.classList.add('active');
  }, 10);

  let secondsLeft = WARNING_DURATION;
  const countdownSpan = document.getElementById('session-countdown');
  countdownSpan.textContent = secondsLeft;

  countdownInterval = setInterval(() => {
    secondsLeft--;
    countdownSpan.textContent = secondsLeft;

    if (secondsLeft <= 0) {
      clearInterval(countdownInterval);
      window.location.href = `${SIARH.apiUrl}/auth/logout?timeout=inactivity`;
    }
  }, 1000);
}

function extendSession() {
  fetch(`${SIARH.apiUrl}/auth/check-session?refresh=1`)
    .then((response) => response.json())
    .then((data) => {
      if (data.authenticated) {
        startIdleTimer();
      } else {
        window.location.href = `${SIARH.apiUrl}/login`;
      }
    })
    .catch((error) => {
      console.error("Error al refrescar la sesión:", error);
      startIdleTimer();
    });
}

// ==================== GRÁFICOS ====================
function initCharts() {
  // Gráfico de asistencia semanal
  const asistenciaChart = document.getElementById("asistencia-chart");
  if (asistenciaChart) {
    loadChartData("asistencia_semanal", renderAsistenciaChart);
  }

  // Gráfico por carrera
  const carreraChart = document.getElementById("carrera-chart");
  if (carreraChart) {
    loadChartData("asistencia_por_carrera", renderCarreraChart);
  }
}

function loadChartData(tipo, callback) {
  fetch(`${SIARH.apiUrl}/dashboard/chart-data?tipo=${tipo}`)
    .then((response) => response.json())
    .then((data) => callback(data))
    .catch((error) =>
      console.error("Error cargando datos del gráfico:", error),
    );
}

function renderAsistenciaChart(data) {
  const ctx = document.getElementById("asistencia-chart").getContext("2d");
  new Chart(ctx, {
    type: "line",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Presentes",
          data: data.presentes,
          borderColor: "#10b981",
          backgroundColor: "rgba(16, 185, 129, 0.1)",
          tension: 0.4,
        },
        {
          label: "Tardanzas",
          data: data.tardanzas,
          borderColor: "#f59e0b",
          backgroundColor: "rgba(245, 158, 11, 0.1)",
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
}

function renderCarreraChart(data) {
  const ctx = document.getElementById("carrera-chart").getContext("2d");
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: data.labels,
      datasets: [
        {
          data: data.values,
          backgroundColor: [
            "#6366f1",
            "#ec4899",
            "#10b981",
            "#f59e0b",
            "#3b82f6",
          ],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
    },
  });
}

// ==================== ACCIONES DE DOCENTES ====================
function deleteDocente(id) {
  if (!confirm("¿Está seguro de eliminar este docente?")) {
    return;
  }

  fetch(`${SIARH.apiUrl}/docentes/delete/${id}`, {
    method: "POST",
  })
    .then((response) => {
      if (response.ok) {
        showAlert("Docente eliminado exitosamente", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showAlert("Error al eliminar docente", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showAlert("Error al eliminar docente", "error");
    });
}

// ==================== ACCIONES DE LICENCIAS ====================
function aprobarLicencia(id) {
  const comentarios = prompt("Comentarios (opcional):");

  const formData = new FormData();
  formData.append("comentarios", comentarios || "");

  fetch(`${SIARH.apiUrl}/licencias/aprobar/${id}`, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (response.ok) {
        showAlert("Licencia aprobada exitosamente", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showAlert("Error al aprobar licencia", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showAlert("Error al aprobar licencia", "error");
    });
}

function rechazarLicencia(id) {
  const comentarios = prompt("Motivo del rechazo:");

  if (!comentarios) {
    showAlert("Debe proporcionar un motivo", "warning");
    return;
  }

  const formData = new FormData();
  formData.append("comentarios", comentarios);

  fetch(`${SIARH.apiUrl}/licencias/rechazar/${id}`, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (response.ok) {
        showAlert("Licencia rechazada", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showAlert("Error al rechazar licencia", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showAlert("Error al rechazar licencia", "error");
    });
}

// ==================== UTILIDADES ====================
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("es-PE", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function formatTime(timeString) {
  if (!timeString) return "N/A";
  return timeString.substring(0, 5);
}

// Exportar funciones globales
window.SIARH = SIARH;
window.showAlert = showAlert;
window.deleteDocente = deleteDocente;
window.aprobarLicencia = aprobarLicencia;
window.rechazarLicencia = rechazarLicencia;
window.initCharts = initCharts;
