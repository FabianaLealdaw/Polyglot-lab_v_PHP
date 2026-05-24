document.addEventListener("DOMContentLoaded", () => {
  const courseSelect = document.getElementById("curso");
  const monthsInput = document.getElementById("meses");
  const plazoInput = document.getElementById("plazo");
  const extrasCheckboxes = document.querySelectorAll(".extra");
  const totalPriceElement = document.getElementById("total-price");
  const form = document.getElementById("formulario-web");
  const privacy = document.getElementById("privacy");

  if (!form || !courseSelect || !monthsInput || !plazoInput || !totalPriceElement) {
    return;
  }

  function calculateTotal() {
    const coursePrice = Number(courseSelect.value);
    const months = Number(monthsInput.value) || 1;
    const plazo = Number(plazoInput.value) || 1;

    let total = coursePrice * months;

    if (months >= 3 && months <= 5) {
      total *= 0.95;
    } else if (months >= 6) {
      total *= 0.9;
    }

    extrasCheckboxes.forEach((extra) => {
      if (extra.checked) {
        total += Number(extra.value);
      }
    });

    let descuento = 0;

    if (plazo <= 7) {
      descuento = 0;
    } else if (plazo <= 30) {
      descuento = 0.05;
    } else if (plazo <= 90) {
      descuento = 0.1;
    } else {
      descuento = 0.15;
    }

    total = total - total * descuento;

    return total;
  }

  function updateTotal() {
    const total = calculateTotal();
    totalPriceElement.textContent = `Total: €${total.toFixed(2)}`;
  }

  courseSelect.addEventListener("change", updateTotal);
  monthsInput.addEventListener("input", updateTotal);
  plazoInput.addEventListener("input", updateTotal);

  extrasCheckboxes.forEach((extra) => {
    extra.addEventListener("change", updateTotal);
  });

  form.addEventListener("submit", (event) => {
    const nombre = document.getElementById("nombre")?.value.trim() || "";
    const apellidos = document.getElementById("apellidos")?.value.trim() || "";
    const telefono = document.getElementById("telefono")?.value.trim() || "";
    const correo = document.getElementById("correo")?.value.trim() || "";
    const descripcion = document.getElementById("descripcion")?.value.trim() || "";

    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,15}$/.test(nombre)) {
      event.preventDefault();
      alert("Invalid name");
      return;
    }

    if (!/^[A-Za-zÀ-ÿ\s]{1,40}$/.test(apellidos)) {
      event.preventDefault();
      alert("Invalid surname");
      return;
    }

    if (!/^[0-9]{9}$/.test(telefono)) {
      event.preventDefault();
      alert("Phone must be 9 digits");
      return;
    }

    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(correo)) {
      event.preventDefault();
      alert("Invalid email");
      return;
    }

    if (descripcion.length < 10 || descripcion.length > 300) {
      event.preventDefault();
      alert("Description must be between 10 and 300 characters");
      return;
    }

    if (!privacy.checked) {
      event.preventDefault();
      alert("You must accept the Privacy Policy");
    }
  });

  form.addEventListener("reset", () => {
    setTimeout(updateTotal, 0);
  });

  updateTotal();
});
