#!/usr/bin/env bash
# One-time setup for a fresh Oracle Cloud Ubuntu 24.04 VM (Ampere/Arm or AMD):
# installs Docker, opens ports 80/443 in the VM firewall, and enables automatic security updates.
# Run as the default `ubuntu` user:  bash scripts/server-setup.sh
set -euo pipefail

echo "==> Updating packages"
sudo apt-get update -y
sudo DEBIAN_FRONTEND=noninteractive apt-get upgrade -y
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y git curl iptables-persistent unattended-upgrades

echo "==> Installing Docker (official convenience script)"
if ! command -v docker >/dev/null 2>&1; then
    curl -fsSL https://get.docker.com | sudo sh
fi
sudo usermod -aG docker "$USER"
sudo systemctl enable --now docker

echo "==> Opening ports 80 and 443"
# Oracle's Ubuntu images ship iptables rules that reject everything except SSH. Insert ACCEPT rules
# before the REJECT rule, then persist them across reboots. (The VCN security list must also allow
# 80/443; see docs/operations/oracle-cloud.md.)
for port in 80 443; do
    sudo iptables -C INPUT -p tcp --dport "$port" -m state --state NEW -j ACCEPT 2>/dev/null \
        || sudo iptables -I INPUT 6 -p tcp --dport "$port" -m state --state NEW -j ACCEPT
done
sudo iptables -C INPUT -p udp --dport 443 -j ACCEPT 2>/dev/null \
    || sudo iptables -I INPUT 6 -p udp --dport 443 -j ACCEPT
sudo netfilter-persistent save

echo "==> Enabling automatic security updates"
sudo dpkg-reconfigure -f noninteractive unattended-upgrades

echo
echo "Done. Log out and back in (so the docker group applies), then continue with the guide:"
echo "  docs/operations/oracle-cloud.md → 'Deploy'"
