// Set current year
    document.getElementById('year').textContent = new Date().getFullYear();

    // Farming Quotes
    const farmingQuotes = [
      {
        quote: "Farming looks mighty easy when your plow is a pencil, and you're a thousand miles from the corn field.",
        author: "DWIGHT D. EISENHOWER"
      },
      {
        quote: "The farmer has to be an optimist or he wouldn't still be a farmer.",
        author: "WILL ROGERS"
      },
      {
        quote: "Agriculture is our wisest pursuit, because it will in the end contribute most to real wealth, good morals, and happiness.",
        author: "THOMAS JEFFERSON"
      },
      {
        quote: "To forget how to dig the earth and to tend the soil is to forget ourselves.",
        author: "MAHATMA GANDHI"
      },
      {
        quote: "The ultimate goal of farming is not the growing of crops, but the cultivation and perfection of human beings.",
        author: "MASANOBU FUKUOKA"
      }
    ];

    let currentQuoteIndex = 0;

    function refreshQuote() {
      currentQuoteIndex = (currentQuoteIndex + 1) % farmingQuotes.length;
      const quote = farmingQuotes[currentQuoteIndex];
      
      const quoteElement = document.getElementById('quote');
      const authorElement = document.getElementById('author');
      
      quoteElement.style.opacity = '0.5';
      
      setTimeout(() => {
        quoteElement.textContent = `"${quote.quote}"`;
        authorElement.textContent = quote.author;
        quoteElement.style.opacity = '1';
      }, 200);
    }

    // Initialize with random quote
    window.onload = function() {
      currentQuoteIndex = Math.floor(Math.random() * farmingQuotes.length);
      refreshQuote();

      // --- NEW: Try to fetch a dynamic quote from OpenAI (non-destructive; fallback to static)
      // Put your OpenAI API key below (keep it secret; do NOT commit to public repos)
      const apiKey = "sk-REPLACE_WITH_YOUR_KEY"; // <-- replace with your OpenAI API key

      // If you don't want the OpenAI call, set enableAIQuote to false
      const enableAIQuote = true;

      if (enableAIQuote && apiKey && apiKey !== "sk-REPLACE_WITH_YOUR_KEY") {
        // call the AI quote fetch; if it fails, the static quote remains
        fetchAIQuote(apiKey);
      }
    };

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    /* -----------------------------
       OpenAI dynamic quote section
       (added; doesn't remove or change existing behavior)
       ----------------------------- */

    // fetchAIQuote: asks OpenAI for a short farming quote and updates the page
    async function fetchAIQuote(apiKey) {
      const quoteElement = document.getElementById('quote');
      const authorElement = document.getElementById('author');

      // show light loading state (non-destructive)
      const previousQuote = quoteElement.textContent;
      const previousAuthor = authorElement.textContent;
      quoteElement.style.opacity = '0.6';
      // keep existing text while fetching

      try {
        const payload = {
          model: "gpt-3.5-turbo",
          messages: [
            { role: "system", content: "You are an assistant that returns a short inspirational quote about farming followed by the author, separated by ' - '. Provide only one quote." },
            { role: "user", content: "Give me a short inspirational quote about agriculture or farming in the format: Quote - Author" }
          ],
          max_tokens: 60,
          temperature: 0.8
        };

        const response = await fetch("https://api.openai.com/v1/chat/completions", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + apiKey
          },
          body: JSON.stringify(payload)
        });

        if (!response.ok) {
          // API returned non-200 -> fallback to previous
          console.warn("OpenAI API returned non-OK:", response.status);
          quoteElement.style.opacity = '1';
          return;
        }

        const data = await response.json();
        const message = data?.choices?.[0]?.message?.content?.trim();
        if (!message) {
          quoteElement.style.opacity = '1';
          return;
        }

        // Parse "Quote - Author" (if author missing, set Unknown)
        let quoteText = message;
        let authorText = "Unknown";

        const parts = message.split(" - ");
        if (parts.length >= 2) {
          authorText = parts.pop().trim();
          quoteText = parts.join(" - ").trim();
        }

        // Remove surrounding quotes if any
        quoteText = quoteText.replace(/^["“”\s]+|["“”\s]+$/g, "");

        // Update UI
        quoteElement.textContent = `"${quoteText}"`;
        authorElement.textContent = authorText;
        quoteElement.style.opacity = '1';

      } catch (error) {
        console.error("Error fetching AI quote:", error);
        // restore previous / fallback (do nothing so static quote remains)
        quoteElement.style.opacity = '1';
      }
    }
