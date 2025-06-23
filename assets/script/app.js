const manualTranslations = {
  ar: {
    home: "الرئيسية",
    about: "من نحن",
    contact: "اتصل بنا",
    services: "خدماتنا",
  },
  en: {
    الرئيسية: "Home",
    "من نحن": "About",
    "اتصل بنا": "Contact",
    خدماتنا: "Services",
  },
};

async function translateText(text, targetLang) {
  const lowerText = text.trim().toLowerCase();
  if (
    manualTranslations[targetLang] &&
    manualTranslations[targetLang][lowerText]
  ) {
    return manualTranslations[targetLang][lowerText];
  }

  const res = await fetch(
    "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" +
      targetLang +
      "&dt=t&q=" +
      encodeURIComponent(text)
  );
  const data = await res.json();
  return data[0][0][0];
}

async function translatePage(lang) {
  const elements = document.querySelectorAll("[data-translate]");
  const promises = [];

  elements.forEach((el) => {
    if (el.classList.contains("no-rtl")) return;

    const original = el.dataset.original || el.textContent;
    if (!el.dataset.original) el.dataset.original = original;
    promises.push(translateText(original, lang));
  });

  const translations = await Promise.all(promises);

  let i = 0;
  elements.forEach((el) => {
    if (el.classList.contains("no-rtl")) return;
    el.textContent = translations[i++];
  });

  if (lang === "ar") {
    document.body.classList.add("arabic");
    document.documentElement.lang = "ar";
    document.documentElement.dir = "rtl";
  } else {
    document.body.classList.remove("arabic");
    document.documentElement.lang = lang;
    document.documentElement.dir = "ltr";
  }

  localStorage.setItem("lang", lang);
}

document.querySelectorAll("[data-lang]").forEach((button) => {
  button.addEventListener("click", () => {
    const lang = button.dataset.lang;
    translatePage(lang);
  });
});

window.addEventListener("DOMContentLoaded", () => {
  const savedLang = localStorage.getItem("lang") || "en";
  translatePage(savedLang);
});
