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

**Site principal** : https://black-lab.fr  
**Mise à jour** : 20 février 2026
