// Fetch the JSON file and dynamically populate content
(async function () {
  // Environment flag
  let environment = "production"; // Default to production
  let debugLog = []; // Collect errors/warnings for debug panel

  const templateRegistry = {}; // Template registry for extensibility
  const transformRegistry = {}; // Transformation registry for extensibility

  try {
    // Fetch content.json and check environment
    const response = await fetch("example.json");
    if (!response.ok) throw new Error(`Failed to fetch JSON: ${response.status}`);
    const data = await response.json();
    environment = data.environment || environment; // Use environment flag if defined
    populateContent(data.content);
    if (environment === "development") renderDebugPanel();
  } catch (error) {
    console.error("Error initializing content population:", error);
  }

  // Populate content into the page
  function populateContent(content) {
    document.querySelectorAll("[data-content]").forEach((element) => {
      const path = element.getAttribute("data-content");
      const value = resolvePath(content, path);

      if (value === undefined) {
        handleMissingContent(path, element);
        return;
      }

      // Handle arrays
      if (Array.isArray(value)) {
        renderArrayContent(value, element);
      }
      // Handle objects
      else if (typeof value === "object") {
        renderObjectContent(value, element);
      }
      // Handle other data types
      else {
        renderPrimitiveContent(value, element, path);
      }
    });
  }

  // Resolve path in JSON
  function resolvePath(obj, path) {
    return path.split(".").reduce((acc, key) => (acc ? acc[key] : undefined), obj);
  }

  // Handle missing content
  function handleMissingContent(path, element) {
    let elementDescription = "<unknown element>";
  
    // Try to capture detailed element information
    if (element.outerHTML) {
      elementDescription = element.outerHTML;
    } else if (element.tagName) {
      // Construct a simplified description from tagName and attributes
      const attributes = Array.from(element.attributes)
        .map((attr) => `${attr.name}="${attr.value}"`)
        .join(" ");
      elementDescription = `<${element.tagName.toLowerCase()} ${attributes}>`;
    }
  
    // Log what is being captured for the element
    console.log("Captured Element Details:", element);
  
    // Generate the debug message
    const message = `Missing content for path: "${path}". Element: ${elementDescription}`;
  
    // Handle development environment behavior
    if (environment === "development") {
      element.innerHTML = `<span class="placeholder">Content not available</span>`;
      console.warn(message);
      debugLog.push(message);
    }
  }
  
  

  // Render array content
  function renderArrayContent(array, element) {
    if (!Array.isArray(array)) {
      console.error(`Expected an array but got:`, array);
      return;
    }
    if (array.length === 0) {
      console.warn(`Array is empty for element:`, element);
      element.innerHTML = "<p>No content available</p>";
      return;
    }
    
    const tag = element.getAttribute("data-content-tag") || "div";
    element.innerHTML = array
      .map((item) => {
        if (typeof item === "object" && item.type) {
          return renderTemplate(item);
        } else if (typeof item === "string") {
          return `<${tag}>${escapeHTML(item)}</${tag}>`;
        } else if (item.category && item.questions) {
          return `
            <div class="faq-category">
              <h3>${escapeHTML(item.category)}</h3>
              <ul>
                ${item.questions
                  .map(
                    (q) => `
                      <li>
                        <strong>${escapeHTML(q.question)}</strong>
                        <p>${escapeHTML(q.answer)}</p>
                      </li>
                    `
                  )
                  .join("")}
              </ul>
            </div>
          `;
        } else if (item.day && item.time) {
          // Handle operating hours
          return `<li>${item.day}: ${item.time}</li>`;
        } else if (typeof item === "object" && item.type) {
          // Handle templated objects (e.g., cards, testimonials)
          return renderTemplate(item);
        } else if (typeof item === "string") {
          // Handle plain strings
          return `<${tag}>${escapeHTML(item)}</${tag}>`;
        } else if (item.platform && item.url && item.iconURL) {
          // Handle social links
          return `
            <a href="${item.url}" target="_blank" class="social-link">
              <img src="${item.iconURL}" alt="${item.platform}" />
              <span>${item.platform}</span>
            </a>
          `;
        } else {
          // Fallback for unknown object structures
          return `<${tag}>${JSON.stringify(item)}</${tag}>`;
        }
      })
      .join("");
  }


  // Render object content
  function renderObjectContent(object, element) {
    if (object.type) {
      element.innerHTML = renderTemplate(object);
    } else {
      element.innerHTML = Object.entries(object)
        .map(([key, value]) => `<strong>${key}:</strong> ${escapeHTML(value)}`)
        .join("<br>");
    }
  }

  // Render primitive content
  function renderPrimitiveContent(value, element, path) {
    const transform = element.getAttribute("data-transform");
    if (transform) value = applyTransform(value, transform);
    if (element.tagName === "IMG") {
      element.src = value;
    } else if (element.tagName === "A") {
      element.href = value;
    } else {
      element.innerHTML = escapeHTML(value);
    }
  }

  // Apply data transformations
  function applyTransform(value, transform) {
    if (transformRegistry[transform]) {
      return transformRegistry[transform](value);
    }
    switch (transform) {
      case "uppercase":
        return value.toString().toUpperCase();
      case "lowercase":
        return value.toString().toLowerCase();
      case "capitalize":
        return value.toString().replace(/\b\w/g, (char) => char.toUpperCase());
      case "date":
        return new Date(value).toLocaleDateString();
      case "currency":
        return new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(value);
      case "special":
        return value.replace(/®/g, "<sup>®</sup>").replace(/™/g, "<sup>™</sup>");
      default:
        return value;
    }
  }

  // Render predefined templates
  function renderTemplate(data) {
    if (templateRegistry[data.type]) {
      return templateRegistry[data.type](data);
    }
    switch (data.type) {
      case "card":
        return `
          <div class="card ${data.class || ""}" style="${data.style || ""}">
            <img src="${data.imageURL}" alt="${data.title || ""}">
            <h3>${escapeHTML(data.title)}</h3>
            <p>${escapeHTML(data.description)}</p>
            ${data.url ? `<a href="${data.url}">Learn More</a>` : ""}
          </div>`;
      case "testimonial":
        return `
          <blockquote class="testimonial ${data.class || ""}" style="${data.style || ""}">
            <p>${escapeHTML(data.text)}</p>
            <footer>${escapeHTML(data.author)}</footer>
          </blockquote>`;
      default:
        return `<div>${JSON.stringify(data)}</div>`;
    }
  }

  // Escape HTML to prevent XSS
  function escapeHTML(str) {
    return str
      .toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }
  

  function renderDebugPanel() {
    console.log("Rendering Debug Panel with debugLog:", debugLog);
  
    const debugPanel = document.createElement("div");
    debugPanel.className = "debug-panel";
  
    // Create the toggle button
    const toggleButton = document.createElement("button");
    toggleButton.id = "toggle-debug-panel";
    toggleButton.textContent = "Toggle Debug Panel";
    toggleButton.addEventListener("click", () => {
      debugLogContainer.classList.toggle("open");
    });
  
    // Create the debug log container
    const debugLogContainer = document.createElement("div");
    debugLogContainer.className = "debug-log";
  
    // Escape and render the debug log messages
    debugLogContainer.innerHTML = debugLog.length > 0
      ? debugLog.map((log) => `<p>${escapeHTML(log)}</p>`).join("")
      : "<p>No logs available.</p>";
  
    // Append elements to the debug panel
    debugPanel.appendChild(toggleButton);
    debugPanel.appendChild(debugLogContainer);
    document.body.appendChild(debugPanel);
  }
  
  // Register additional templates
  templateRegistry["faq"] = (data) => `
    <div class="faq-category">
      <h3>${escapeHTML(data.category)}</h3>
      <ul>
        ${data.questions
          .map(
            (q) => `
              <li>
                <strong>${escapeHTML(q.question)}</strong>
                <p>${escapeHTML(q.answer)}</p>
              </li>
            `
          )
          .join("")}
      </ul>
    </div>`;
  
  templateRegistry["operating-hours"] = (data) => `
    <ul>
      ${data
        .map((item) => `<li>${item.day}: ${item.time}</li>`)
        .join("")}
    </ul>`;
  
  transformRegistry.capitalizeWords = (value) =>
    value.replace(/\b\w/g, (char) => char.toUpperCase());

})();
