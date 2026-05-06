# IrTwitch Web Gateway & Backend Infrastructure

> **⚠️ Engineering & Contextual Disclaimer:**
> This platform was developed as an engineering solution to study **network resilience, continuous media delivery, and traffic routing in highly constrained and restricted network environments** (specifically addressing severe network anomalies and DNS disruptions in Iran mid-2022). To strictly comply with copyright and digital distribution norms, this platform operated entirely on an **opt-in authorization model**.

The IrTwitch Web Gateway is the core backend architecture and user interface built to ensure high-availability access to live video streams. Developed using **PHP and the Lumen micro-framework**, it served as a high-performance syndication platform capable of retrieving and relaying live streams with minimal latency.

## 🚀 Technical Highlights & Scale

1. **Resilient Media Routing:** 
   Designed to overcome extreme network filtering, packet loss, and connection resets. The backend dynamically retrieved live stream metadata and routed video streams through optimized edge nodes, ensuring stable and uninterrupted playback for end-users in restricted regions.

2. **Strict Authorization Pipeline (Access Control):**
   To maintain ethical standards and respect intellectual property, the system implemented a strict opt-in workflow. The core engine was programmed to only relay content for creators who explicitly granted permission, demonstrating a secure approach to API integration and digital rights validation.

3. **High-Concurrency & Scalability:** 
   The choice of Lumen—a blazing-fast micro-framework—was a deliberate architectural decision to handle massive volumes of concurrent requests and lightweight routing. The system's architecture successfully scaled in production, smoothly handling over **55,000 Unique Monthly Active Users (MAU)** during its peak operation.

## 🔗 Archived Demo

An archived functional snapshot demonstrating the platform's UI, dynamic rendering, and stream integration can be accessed via the Wayback Machine:
* [IrTwitch Platform Demo (Archived)](https://web.archive.org/web/20230711000318/https://twfa.cfd/)

## 👨‍💻 Developer

Architected and developed by [masterking32](https://github.com/masterking32), with a focus on scalable backend architecture, micro-framework optimization, and network resilience.
