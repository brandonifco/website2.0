// Load the content JSON dynamically
async function fetchContent() {
    try {
        const response = await fetch('content.json');
        return await response.json();
    } catch (error) {
        console.error('Error fetching content:', error);
        return {};
    }
}

// Generate HTML for menus, including nested submenus
function generateMenuHTML(menuItems) {
    return menuItems
        .map((item) => {
            // Replace "®" with "<sup>®</sup>" in the label
            const formattedLabel = item.label.replace(/®/g, '<sup>®</sup>');

            if (item.submenu) {
                return `
                    <li class="menu-item has-submenu"><a>${formattedLabel}</a>
                        <ul class="submenu">
                            ${generateMenuHTML(item.submenu)}
                        </ul>
                    </li>
                `;
            } else {
                return `<li class="menu-item"><a href="${item.url}">${formattedLabel}</a></li>`;
            }
        })
        .join('');
}


// Populate content into the page dynamically
function populateContent(content) {
    const contentElements = document.querySelectorAll('[data-content]');
    const desktopMenu = document.querySelector('.desktop-menu');
    const mobileMenu = document.querySelector('.mobile-menu');

    contentElements.forEach((element) => {
        const key = element.getAttribute('data-content');
        let value = content;

        const arrayKeyMatch = key.match(/(.+\[(\d+)\])/);
        if (arrayKeyMatch) {
            const baseKey = arrayKeyMatch[1];
            const index = parseInt(arrayKeyMatch[2], 10);
            const keys = baseKey.split('.');
            for (const k of keys) {
                value = value[k] || null;
            }
            value = Array.isArray(value) && value[index] !== undefined ? value[index] : null;
        } else {
            const keys = key.split('.');
            for (const k of keys) {
                value = value[k] || null;
            }
        }

        // Special case: Populate operating hours and holidays
        if (element.id === 'hours-list' && Array.isArray(value)) {
            element.innerHTML = value
                .map(item => `<li>${item.day}: ${item.time}</li>`)
                .join('');
        } else if (element.id === 'holidays-list' && Array.isArray(value)) {
            element.innerHTML = value.map(holiday => `<li>${holiday}</li>`).join('');
        } else if (value !== null) {
            // Default behavior for other elements
            if (element.tagName === 'IMG' && key.includes('ImageURL')) {
                element.src = value;
            } else if (element.tagName === 'A' && key.includes('url')) {
                element.href = value;
            } else if (element.tagName === 'IFRAME' && key.includes('embedURL')) {
                element.src = value;
            } else if (Array.isArray(value)) {
                element.innerHTML = value
                    .map((item) => {
                        if (item.title && item.description && item.url) {
                            return `
                                <div class="service-card">
                                    <a href="${item.url}">${item.title}</a>
                                    <p>${item.description}</p>
                                </div>`;
                        } else if (item.label && item.url) {
                            return `<a href="${item.url}">${item.label}</a>`;
                        } else if (item.text && item.author) {
                            return `
                                <div class="testimonial">
                                    <p>"${item.text}"</p>
                                    <p><strong>- ${item.author}</strong></p>
                                </div>`;
                        } else if (item.name && item.logoURL) {
                            return `
                                <div class="trust-indicator">
                                    <img src="${item.logoURL}" alt="${item.name}">
                                    <p>${item.name}</p>
                                </div>`;
                        } else if (item.platform && item.url && item.iconURL) {
                            return `
                                <a href="${item.url}" class="social-media-link">
                                    <img src="${item.iconURL}" alt="${item.platform}">
                                </a>`;
                        } else if (item.title && item.description) {
                            // Handle items with title and description (e.g., issuesAddressed)
                            return `
                                <div class="issue-card">
                                    <br>
                                    <img src="${item.iconURL}" alt="${item.title}">
                                    <h2>${item.title}</h2>
                                    <p>${item.description}</p>
                                </div>`;
                        } else if (item.category && item.questions) {
                            // Handle FAQ categories and their questions
                            return `
                                <div class="faq-category">
                                    
                                    <ul>
                                        ${item.questions
                                    .map(
                                        (question) => `
                                                    
                                                        <h2>${question.question}</h2>
                                                        <p>${question.answer}</p>
                                                        <br>
                                                    `
                                    )
                                    .join('')}
                                    </ul>
                                </div>`;
                        } else if (typeof item === 'string') {
                            return `<li>${item}</li>`;
                        } else {
                            return `<div>${JSON.stringify(item)}</div>`;
                        }
                    })
                    .join('');
            } else {
                element.textContent = value;
            }
        } else {
            console.warn(`No content found for key: ${key}`);
        }
    });

    if (content.header && content.header.menuItems) {
        if (desktopMenu) {
            desktopMenu.innerHTML = generateMenuHTML(content.header.menuItems.desktop);
        }
        if (mobileMenu) {
            mobileMenu.innerHTML = generateMenuHTML(content.header.menuItems.mobile);
        }
    }
}


// Initialize the dynamic content population
document.addEventListener('DOMContentLoaded', async () => {
    const content = await fetchContent();
    populateContent(content);
});
