---
title: An Intro to my Homelab
author: Archey Barrell
published_at: 2025-12-06 15:52:00
tags: ["homelab"]
---

I've been building a homelab since 2024. This is the obligatory introduction post,
so that the rest of the posts I write about it have something to point back to.

![The homelab rack](/media/homelab.jpg)

_Are network indicator lights a suitable alternative if you don't have a christmas tree?_

### What's on it

Mostly DNS at the moment. `ns2.archbar.me` is the box in the bottom right hand corner
of the photo, and it does a fair amount of the heavy lifting. Alongside that there are a
handful of services that I actually use day to day:

- An IRC bouncer, so I don't miss things whilst my laptop is shut.
- Grafana and VictoriaMetrics, for graphs of things that mostly don't need graphing.

Everything is configured from a repository, rather than from whatever I could remember
typing into an SSH session six months ago.

### How it's put together

For now it's docker containers set up via ansible. It's not glamorous, but it's
reproducible, and the whole thing can be rebuilt from the config in version control if I
manage to break something badly enough.

The longer term aim is a kubernetes cluster running most of the services, probably bar
the most important ones, in case I screw things up. There's not much point in having a
nameserver that depends on a control plane that depends on the nameserver.

A while ago, when I was sixteen, I built a box for putting lots of Raspberry Pis in. I
think that's the solution for the inevitable increase in the number of nodes I end up
with.

### What's next

There are a few things I want to write up: what the ansible setup actually looks like,
how the monitoring is put together, and eventually how the move to k8s goes (or doesn't).
The first of them is [how I do IPv6 at home](/blog/posts/2025-12-08-ipv6-at-home).
