{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = {
    self,
    nixpkgs,
    flake-utils,
    ...
  }:
    flake-utils.lib.eachDefaultSystem (system: let
      pkgs = nixpkgs.legacyPackages.${system};
    in {
      packages = {
        bakule-timer-uzdil = pkgs.callPackage ./bakule-timer.nix {pdf_url = "https://bc.zde.uzdil.cz/main.pdf";};
      };
      defaultPackage = self.packages.${system}.bakule-timer-uzdil;
    });
}
