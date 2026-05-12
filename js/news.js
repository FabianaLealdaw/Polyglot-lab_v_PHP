const newsContainer = document.getElementById("news-container");

// Se utiliza fetch para obtener los datos del archivo JSON
fetch("data/news.json")
  .then((response) => response.json())
  .then((news) => {
    // Se recorre cada noticia del archivo JSON
    news.forEach((item) => {
      // Se crea el elemento principal de la tarjeta de noticia
      const article = document.createElement("article");
      article.classList.add("news-card");

      // Se crea y se añade el título de la noticia
      const title = document.createElement("h3");
      title.textContent = item.title;

      // Se crea y se añade la fecha de la noticia
      const date = document.createElement("p");
      date.classList.add("news-date");
      date.textContent = item.date;

      // Se crea y se añade el texto resumen de la noticia
      const text = document.createElement("p");
      text.textContent = item.text;

      // Se añaden todos los elementos a la tarjeta
      article.appendChild(title);
      article.appendChild(date);
      article.appendChild(text);

      // Se añade la tarjeta al contenedor de noticias
      newsContainer.appendChild(article);

      // Al hacer clic en una noticia se muestra su contenido completo
      article.addEventListener("click", () => {
        const detailContainer = document.getElementById(
          "news-detail-container",
        );

        // Se limpia el contenido anterior de la sección de detalle
        detailContainer.innerHTML = "";

        // Se crea el artículo con la noticia desarrollada
        const fullArticle = document.createElement("article");
        fullArticle.classList.add("news-detail-article");

        const fullTitle = document.createElement("h3");
        fullTitle.textContent = item.title;

        const fullDate = document.createElement("p");
        fullDate.classList.add("news-detail-date");
        fullDate.textContent = item.date;

        const fullText = document.createElement("p");
        fullText.textContent = item.fullText;

        // Se añaden los elementos al artículo completo
        fullArticle.appendChild(fullTitle);
        fullArticle.appendChild(fullDate);
        fullArticle.appendChild(fullText);

        // Se muestra la noticia desarrollada en la sección correspondiente
        detailContainer.appendChild(fullArticle);
        // Botón para ir a la página de presupuesto
        const budgetLink = document.createElement("a");
        budgetLink.href = "views/get_a_quote.html";
        budgetLink.textContent = "Request a quote";
        budgetLink.classList.add("news-budget-link");

        fullArticle.appendChild(budgetLink);

        // Se realiza un desplazamiento suave hasta la sección de detalle
        const targetPosition = document.getElementById("news-detail").offsetTop;

        window.scrollTo({
          top: targetPosition - 80,
          behavior: "smooth",
        });
      });
    });
  })
  .catch((error) => {
    console.error("Error al cargar las noticias:", error);
  });
