# Black-Lab Toolbox

![License](https://img.shields.io/badge/License-CC%20BY--NC%204.0-ff1654)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![Live](https://img.shields.io/badge/Live-black--lab.fr-ff1654)

**Développé par** : [Loïc HÉRAUDEAU](https://black-lab.fr) (@D-Goth)  
**Contexte** : Projets personnels développés en parallèle d'expériences professionnelles  
**Objectif** : Démontrer une expertise technique solide en développement full-stack avec focus privacy & performance

---

## Black-Lab Toolbox – Liste des outils (février 2026)

Collection d'outils PHP développés pour faciliter diverses tâches en ligne.  
Black‑Lab Toolbox est conçue selon une approche "privacy‑first".

---

## 🛠️ Stack technique 

**Langages principaux** 
- PHP
- JavaScript (vanilla)
- HTML5
- CSS3

**Librairies légères & maîtrisées** 
- vis-network
- Leaflet.js
- Librairies PDF (JSZip / PDF-Lib / PDF.js selon l'outil)
- APIs Web natives (Canvas, Storage, Geolocation — toujours optionnelles et consenties)

**Philosophie Black‑Lab** 
- Zéro tracking
- Zéro publicité
- Dépendances externes limitées au strict nécessaire
- Appels réseau transparents et justifiés

---

## 📊 Parcours des projets

Ces outils ont été développés progressivement, chacun apportant de nouvelles compétences :

### **Phase 1 : Fondamentaux (2023)**
- **Password Generator** → Génération sécurisée, crypto API native
- **QR Code Generator** → Canvas manipulation, export fichiers
- **Weather Dashboard** → Appels API REST, gestion async

### **Phase 2 : Intégration de libs (2024)**
- **DNS/IP Lookup** → Leaflet.js, géolocalisation, parsing réseau
- **MindMap Creator** → vis-network, graphes interactifs, physique de particules
- **Dashboard RPi** → SSH2 PHP, monitoring temps réel, GPIO

### **Phase 3 : Algorithmique avancée (2025)**
- **ChromaLab** → Conversion OKLCH pure, maths colorimétriques
- **PDF Forge** → Manipulation multi-lib (pdf-lib, pdf.js, Tesseract OCR)
- **Prompt Lab** → Scoring propriétaire multi-axes, système de briques modulaire

### **Phase 4 : Fusion & Optimisation (2026)**
- **CleanShell/Script Whisperer** → Regex patterns avancés, analyse statique
- **Mermaid Lab** → Diagrammes programmables, export multi-format

---

## 🎯 Choix techniques par projet

| Outil | Stack | Justification |
|-------|-------|---------------|
| **Password Generator** | Crypto API native | Sécurité maximale, pas de lib externe à auditer |
| **QR Code Generator** | qrcodejs | Lib légère éprouvée, personnalisation complète |
| **Weather Dashboard** | OpenWeatherMap API | API publique gratuite, données fiables |
| **DNS/IP Lookup** | Leaflet.js, UAParser.js | Cartographie interactive + parsing User-Agent |
| **MindMap Creator** | vis-network | Moteur physique intégré, gestion auto des layouts complexes |
| **Dashboard RPi** | SSH2 PHP extension | Connexion directe sécurisée, pas de couche intermédiaire |
| **ChromaLab** | Algorithme OKLCH pur | Contrôle total sur la précision colorimétrique |
| **PDF Forge** | pdf-lib, pdf.js, Tesseract.js | Client-side complet (privacy) + OCR automatique |
| **Prompt Lab** | Logique propriétaire | Scoring sur-mesure, pas de framework IA générique |
| **CleanShell** | Regex patterns custom | Anonymisation ciblée, pas de sur-dépendance |
| **Script Whisperer** | Analyse statique custom | Audit de sécurité spécifique au contexte |
| **Mermaid Lab** | Mermaid.js, html2canvas | Diagrammes standards + export haute résolution |

---

## 💡 Genèse de quelques projets

### **PDF Forge**
**Besoin** : Manipuler des PDFs sans les uploader sur des services tiers  
**Contrainte** : Tout côté client pour garantir la confidentialité  
**Défi technique** : Combiner 3 libs (pdf-lib, pdf.js, Tesseract) avec fallback OCR automatique

### **ChromaLab**
**Constat** : Les générateurs de palettes utilisent HSL/RGB, pas perceptuellement uniformes  
**Solution** : Implémenter OKLCH (espace colorimétrique moderne) en JS pur  
**Résultat** : Palettes plus harmonieuses, export multi-format

### **Prompt Lab**
**Observation** : Beaucoup d'outils IA génèrent du texte sans analyse structurée  
**Approche** : Créer un système de scoring multi-axes (clarté, cohérence, créativité...)  
**Évolution** : 300+ briques modulaires, scoring pondéré dynamique

### **Dashboard RPi**
**Cas d'usage** : Surveiller des Raspberry Pi à distance sans installer d'agent lourd  
**Technique** : Connexion SSH2 directe, lecture GPIO en temps réel  
**Limite acceptée** : Nécessite credentials SSH (pas de magie)

---

## Installation locale

Chaque outil fonctionne en HTML/CSS/JS/PHP simple.  
Clonez le dépôt, placez le dossier de l'outil dans un serveur local (Apache, Nginx, Laragon, WAMP…) et ouvrez `index.php`.

**Démo complète et filtrable** :  
https://black-lab.fr/toolbox/

**Code source organisé** (un dossier par outil ; en cours de création côté GitHub) :  
https://github.com/D-Goth/black-lab-toolbox

---

## 📋 Liste complète des outils

| Icône | Nom de l'outil              | Description                                              | Catégorie                     | Démo en ligne                                      |
|-------|-----------------------------|----------------------------------------------------------|-------------------------------|----------------------------------------------------|
| 🪄    | Aura Control                | Contrôle avancé des auras et permissions                 | Sécurité & Code               | https://black-lab.fr/aura-control/                 |
| 🎨    | Badges Generator            | Générateur de badges et QR codes stylés                  | Création Visuelle             | https://black-lab.fr/badges-generator/             |
| 🧹    | CleanShell Anonymizer       | Nettoyage et anonymisation de scripts shell              | Sécurité & Code               | https://black-lab.fr/cleanshell-anonymizer/        |
| 🎨    | Chroma Lab                  | Générateur de palettes de couleurs harmonieuses          | Création Visuelle             | https://black-lab.fr/chroma-lab/                   |
| 🍓    | Dashboard RPi               | Supervision Raspberry Pi en temps réel                   | Réseau & Systèmes (Ops)       | https://black-lab.fr/dashboard-rpi/                |
| 🌐    | DNS Lookup                  | Recherche DNS avancée et traçage IP                      | Réseau & Systèmes (Ops)       | https://black-lab.fr/dns-lookup/                   |
| 📍    | IP Tracer                   | Géolocalisation et traçage IP détaillé                   | Réseau & Systèmes (Ops)       | https://black-lab.fr/ip-tracer/                    |
| 🧩    | MindMap Creator             | Création de mindmaps interactives                        | Productivité                  | https://black-lab.fr/mindmap-creator/              |
| 🔑    | Passwords Generator         | Générateur de mots de passe ultra-sécurisés              | Sécurité & Code               | https://black-lab.fr/passwords-generator/          |
| 📄    | PDF Forge                   | Manipulation PDF complète côté client                    | Productivité                  | https://black-lab.fr/pdf-forge/                    |
| 🧠    | Prompt Lab                  | 300+ briques expertes, scoring multi-axes                | IA Générative                 | https://black-lab.fr/prompt-lab/                   |
| 🔳    | QR Code Generator           | Générateur QR avancé et personnalisé                     | Création Visuelle             | https://black-lab.fr/qr-code-generator/            |
| 🧹    | Script Whisperer            | Moteur d'analyse et évaluation de scripts                | Sécurité & Code               | https://black-lab.fr/script-whisperer/             |
| ☀️    | Weather Dashboard Pro       | Tableau de bord météo personnalisé                       | Productivité                  | https://black-lab.fr/weather-dashboard-pro/        |

---

## 💼 Compétences Techniques Démontrées

### **Architecture & Design Patterns**
- Architecture modulaire avec séparation des responsabilités
- Pattern MVC léger (sans framework lourd)
- Design System cohérent (variables CSS, thème dark unifié)

### **Frontend**
- JavaScript vanilla ES6+ (async/await, fetch, modules)
- Manipulation DOM performante (event delegation, debouncing)
- Canvas API, Storage API, Geolocation API
- CSS avancé (Grid, Flexbox, animations, backdrop-filter)

### **Algorithmique**
- Conversion colorimétrique OKLCH ↔ RGB (maths pures)
- Scoring multi-axes avec pondération dynamique
- OCR via Tesseract.js avec fallback automatique

### **APIs & Intégrations**
- REST APIs (fetch, gestion erreurs, rate limiting)
- Leaflet.js (cartographie interactive)
- pdf-lib/pdf.js (manipulation PDF côté client)
- Mermaid.js (diagrammes programmables)

### **Sécurité & Privacy**
- Input sanitization (protection XSS)
- Client-side processing (pas de données serveur)
- CORS handling transparent
- Anonymisation de données sensibles (CleanShell)

### **UX/UI**
- Interface dark theme cohérente (Glass morphism)
- Responsive mobile-first
- Feedback utilisateur (toasts, progress bars, états de chargement)
- Accessibility (ARIA labels, keyboard navigation)

---

## 🚧 En cours de développement

**Nouveaux outils**
- Éditeur Markdown avancé (syntax highlighting, export multi-format)
- Analyseur de logs serveur (parsing intelligent, détection anomalies)
- Outil de diff visuel (comparaison fichiers avec highlighting)

**Améliorations prévues**
- Export PNG/SVG pour MindMap Creator
- Mode collaboratif temps réel pour certains outils (via WebSockets)
- API REST publique pour accès programmatique à certaines fonctionnalités

*Note : Ces projets sont développés selon le temps disponible, sans engagement de date.*

---

## Disclaimer sur l'apparence

Les outils sont déployés sur un site WordPress avec un thème actif.  
Certaines variations visuelles (boutons, polices, marges, effets, etc.) peuvent apparaître selon le thème, les plugins CSS ou les mises à jour du site.  
Le code source ici présent est la version « brute » (HTML + CSS + JS vanilla) et peut être testé indépendamment dans un environnement statique ou un autre WordPress.

---

## Licence

Creative Commons BY-NC 4.0  

![CC BY-NC 4.0](https://github.com/user-attachments/assets/2d814f30-2ff8-463a-b1ab-e05bf2d47e54)

https://creativecommons.org/licenses/by-nc/4.0/deed.fr

Pour une licence commerciale ou une collaboration : contact@black-lab.fr

---
---

# Black-Lab Toolbox

**Developed by**: Loïc HÉRAUDEAU (@D-Goth)  
**Context**: Personal projects developed alongside professional experience  
**Objective**: Demonstrate strong full-stack technical expertise with a strong focus on privacy & performance

## Black-Lab Toolbox – Tools Overview (February 2026)

A collection of PHP tools developed to simplify various online tasks.  
Black-Lab Toolbox follows a **privacy-first** approach.

### 🛠️ Technical Stack

**Core Languages**  
- PHP  
- JavaScript (vanilla)  
- HTML5  
- CSS3  

**Lightweight & Controlled Libraries**  
- vis-network  
- Leaflet.js  
- PDF libraries (JSZip / PDF-Lib / PDF.js depending on the tool)  
- Native Web APIs (Canvas, Storage, Geolocation — always optional and consent-based)

### Black-Lab Philosophy

- Zero tracking  
- Zero advertising  
- External dependencies limited to the strict minimum  
- Transparent and justified network calls

### 📊 Project Journey

These tools were developed progressively, each adding new skills:

**Phase 1: Fundamentals (2023)**  
- Password Generator → Secure generation, native Crypto API  
- QR Code Generator → Canvas manipulation, file export  
- Weather Dashboard → REST API calls, async management  

**Phase 2: Library Integration (2024)**  
- DNS/IP Lookup → Leaflet.js, geolocation, network parsing  
- MindMap Creator → vis-network, interactive graphs, particle physics  
- Dashboard RPi → SSH2 PHP, real-time monitoring, GPIO  

**Phase 3: Advanced Algorithmics (2025)**  
- ChromaLab → Pure OKLCH conversion, colorimetry math  
- PDF Forge → Multi-lib manipulation (pdf-lib, pdf.js, Tesseract OCR)  
- Prompt Lab → Proprietary multi-axis scoring, modular brick system  

**Phase 4: Fusion & Optimization (2026)**  
- CleanShell / Script Whisperer → Advanced regex patterns, static analysis  
- Mermaid Lab → Programmable diagrams, multi-format export  

### 🎯 Technical Choices by Project

| Tool                     | Stack                              | Justification                                                                 |
|--------------------------|------------------------------------|-------------------------------------------------------------------------------|
| Password Generator       | Crypto API native                  | Maximum security, no external library to audit                                |
| QR Code Generator        | qrcodejs                           | Lightweight proven library, full customization                                |
| Weather Dashboard        | OpenWeatherMap API                 | Free public API, reliable data                                                |
| DNS/IP Lookup            | Leaflet.js, UAParser.js            | Interactive mapping + User-Agent parsing                                      |
| MindMap Creator          | vis-network                        | Built-in physics engine, automatic layout management                          |
| Dashboard RPi            | SSH2 PHP extension                 | Direct secure connection, no intermediate layer                               |
| ChromaLab                | Pure OKLCH algorithm               | Full control over colorimetric accuracy                                       |
| PDF Forge                | pdf-lib, pdf.js, Tesseract.js      | Complete client-side (privacy) + automatic OCR fallback                       |
| Prompt Lab               | Proprietary logic                  | Custom multi-axis scoring, no generic AI framework                            |
| CleanShell               | Custom regex patterns              | Targeted anonymization, no over-dependency                                    |
| Script Whisperer         | Custom static analysis             | Security audit specific to context                                            |
| Mermaid Lab              | Mermaid.js, html2canvas            | Standard diagrams + high-resolution export                                    |

### 💡 Genesis of a Few Projects

**PDF Forge**  
Need: Manipulate PDFs without uploading them to third-party services  
Constraint: Everything client-side to guarantee confidentiality  
Technical challenge: Combine 3 libraries (pdf-lib, pdf.js, Tesseract) with automatic OCR fallback  

**ChromaLab**  
Observation: Palette generators use HSL/RGB, which are not perceptually uniform  
Solution: Implement pure OKLCH (modern color space) in JavaScript  
Result: More harmonious palettes, multi-format export  

**Prompt Lab**  
Observation: Many AI tools generate text without structured analysis  
Approach: Create a multi-axis scoring system (clarity, coherence, creativity…)  
Evolution: 300+ modular bricks, dynamic weighted scoring  

**Dashboard RPi**  
Use case: Monitor Raspberry Pi remotely without heavy agents  
Technique: Direct SSH2 connection, real-time GPIO reading  
Accepted limitation: Requires SSH credentials (no magic)

### Installation locale

Each tool runs on simple HTML/CSS/JS/PHP.  
Clone the repository, place the tool folder on a local server (Apache, Nginx, Laragon, WAMP…) and open index.php.

### Live Demo

Complete and filterable demo:  
https://black-lab.fr/toolbox/

Organized source code (one folder per tool – currently being created on GitHub):  
https://github.com/D-Goth/black-lab-toolbox

### 📋 Full Tool List

| Icon | Tool Name                  | Description                                              | Category                      | Live Demo                                          |
|------|----------------------------|----------------------------------------------------------|-------------------------------|----------------------------------------------------|
| 🪄   | Aura Control               | Advanced control of auras and permissions                | Security & Code               | https://black-lab.fr/aura-control/                 |
| 🎨   | Badges Generator           | Generator of styled badges and QR codes                  | Visual Creation               | https://black-lab.fr/badges-generator/             |
| 🧹   | CleanShell Anonymizer      | Cleaning and anonymization of shell scripts              | Security & Code               | https://black-lab.fr/cleanshell-anonymizer/        |
| 🎨   | Chroma Lab                 | Generator of harmonious color palettes                   | Visual Creation               | https://black-lab.fr/chroma-lab/                   |
| 🍓   | Dashboard RPi              | Real-time Raspberry Pi supervision                       | Network & Systems (Ops)       | https://black-lab.fr/dashboard-rpi/                |
| 🌐   | DNS Lookup                 | Advanced DNS lookup and IP tracing                       | Network & Systems (Ops)       | https://black-lab.fr/dns-lookup/                   |
| 📍   | IP Tracer                  | Detailed IP geolocation and tracing                      | Network & Systems (Ops)       | https://black-lab.fr/ip-tracer/                    |
| 🧩   | MindMap Creator            | Creation of interactive mindmaps                         | Productivity                  | https://black-lab.fr/mindmap-creator/              |
| 🔑   | Passwords Generator        | Ultra-secure password generator                          | Security & Code               | https://black-lab.fr/passwords-generator/          |
| 📄   | PDF Forge                  | Complete client-side PDF manipulation                    | Productivity                  | https://black-lab.fr/pdf-forge/                    |
| 🧠   | Prompt Lab                 | 300+ expert bricks, multi-axis scoring                   | Generative AI                 | https://black-lab.fr/prompt-lab/                   |
| 🔳   | QR Code Generator          | Advanced and personalized QR code generator              | Visual Creation               | https://black-lab.fr/qr-code-generator/            |
| 🧹   | Script Whisperer           | Script analysis and evaluation engine                    | Security & Code               | https://black-lab.fr/script-whisperer/             |
| ☀️   | Weather Dashboard Pro      | Personalized weather dashboard                           | Productivity                  | https://black-lab.fr/weather-dashboard-pro/        |

### 💼 Demonstrated Technical Skills

**Architecture & Design Patterns**  
- Modular architecture with separation of concerns  
- Lightweight MVC pattern (no heavy framework)  
- Consistent design system (CSS variables, unified dark theme)

**Frontend**  
- Vanilla JavaScript ES6+ (async/await, fetch, modules)  
- Performant DOM manipulation (event delegation, debouncing)  
- Canvas API, Storage API, Geolocation API  
- Advanced CSS (Grid, Flexbox, animations, backdrop-filter)

**Algorithmics**  
- Pure OKLCH ↔ RGB color conversion (maths only)  
- Dynamic multi-axis weighted scoring  
- Tesseract.js OCR with automatic fallback

**APIs & Integrations**  
- REST APIs (fetch, error handling, rate limiting)  
- Leaflet.js (interactive mapping)  
- pdf-lib / pdf.js (client-side PDF manipulation)  
- Mermaid.js (programmable diagrams)

**Security & Privacy**  
- Input sanitization (XSS protection)  
- Client-side processing (no server data)  
- Transparent CORS handling  
- Sensitive data anonymization (CleanShell)

**UX/UI**  
- Consistent dark theme interface (Glass morphism)  
- Mobile-first responsive design  
- User feedback (toasts, progress bars, loading states)  
- Accessibility (ARIA labels, keyboard navigation)

### 🚧 Work in Progress

**Upcoming tools**  
- Advanced Markdown editor (syntax highlighting, multi-format export)  
- Server log analyzer (intelligent parsing, anomaly detection)  
- Visual diff tool (file comparison with highlighting)

**Planned improvements**  
- PNG/SVG export for MindMap Creator  
- Real-time collaborative mode for some tools (via WebSockets)  
- Public REST API for programmatic access to certain features

Note: These projects are developed in available time, without any release date commitment.

### Disclaimer on Appearance

These tools are deployed on a WordPress site with an active theme.  
Visual variations (buttons, fonts, margins, effects, etc.) may appear depending on the theme, CSS plugins, or site updates.  
The source code provided here is the “raw” version (HTML + CSS + vanilla JS) and can be tested independently in a static environment or another WordPress instance.

### License

Creative Commons BY-NC 4.0  
https://creativecommons.org/licenses/by-nc/4.0/deed.fr

For commercial licensing or collaboration: contact@black-lab.fr

Main site: https://black-lab.fr  
Last updated: February 20, 2026

---

**Site principal** : https://black-lab.fr  
**Mise à jour** : 20 février 2026
